<?php

namespace App\Http\Controllers;

use App\Models\KYCData;
use App\Models\Permission;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Commission;
use Carbon\Carbon;
use App\Models\User;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class EmployeeController extends Controller
{
    protected $mailService;

    /**
     * Constructor to initialize MailService.
     *
     * @param MailService $mailService
     */
    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $users = User::with('branch')
                    ->get()
                    ->map(function ($user) {
                        return [
                            'name' => ucwords($user->name),
                            'email' => $user->email,
                            'phone' => $user->phone,
                            'branch' => ucwords(optional($user->branch)->name ?? 'N/A'), // Default to 'N/A' if branch is null
                            'status' => $user->email_verified_at ? 'Active' : 'Inactive',
                            'action' => view('portal.employee.partials.actions', ['user' => $user])->render(),
                            'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                        ];
                    });

                return DataTables::of($users)->make(true);
            }

            return view('portal.employee.index');
        } catch (\Exception $exception) {
            // Log the exception details
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            // Return a general error message
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('portal.employee.create',);
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(Request $request)
    {
        // Validate the input data
        $validatedData = $request->validate([
            'branch' => 'required|exists:branches,id',
            'user_name' => 'required|string|unique:users,user_name',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:15|unique:users,phone',
            'id_number' => 'required|string|max:20|unique:users,id_number',
            'commission_rate' => 'required',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'viewable_branches' => 'nullable|array',
            'viewable_branches.*' => 'exists:branches,id',
            'viewable_catalogues' => 'nullable|array',
            'viewable_catalogues.*' => 'exists:catalogues,id',
            'viewable_products' => 'nullable|array',
            'viewable_products.*' => 'exists:products,id',
        ]);

        $password = genPassword();

        // Use a database transaction
        DB::beginTransaction();

        try {
            // Create the user
            $user = User::create([
                'branch_id' => $validatedData['branch'],
                'user_name' => $validatedData['user_name'],
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'],
                'id_number' => $validatedData['id_number'],
                'commission_rate' => $validatedData['commission_rate'],
                'password' => Hash::make($password),
                'created_by' => Auth::user()->id,
            ]);

            // Attach permissions
            if (!empty($validatedData['permissions'])) {
                foreach ($validatedData['permissions'] as $permissionId) {
                    $permissionData = [
                        'permission_id' => $permissionId,
                        'selected_branches' => $permissionId == Permission::where('slug', 'manager_branch')->value('id')
                            ? json_encode($validatedData['viewable_branches'] ?? [])
                            : null,
                        'selected_products' => $permissionId == Permission::where('slug', 'product_view')->value('id')
                            ? json_encode($validatedData['viewable_products'] ?? [])
                            : null,
                        'selected_catalogues' => $permissionId == Permission::where('slug', 'catalogue_view')->value('id')
                            ? json_encode($validatedData['viewable_catalogues'] ?? [])
                            : null,
                    ];

                    $user->permissions()->attach($permissionId, $permissionData);
                }
            }

            // Add/update any uploaded photo
            $kyc_docs = kyc_docs();

            // Validate the request data for images dynamically
            foreach ($kyc_docs as $key => $value) {
                $validationRules[$key] = 'nullable|image|mimes:jpeg,png,jpg|max:2048'; // Limit to 2MB images
            }

            $request->validate($validationRules);

            // Store each uploaded photo
            foreach ($kyc_docs as $key => $value) {
                if ($request->hasFile($key)) {
                    // Generate a unique file name
                    $fileName = $key . '_' . time() . '.' . $request->file($key)->getClientOriginalExtension();
                    // Move the uploaded file to the public/assets/images/kyc_docs directory
                    $photoPath = $request->file($key)->move(public_path('assets/images/kyc_docs'), $fileName);
                    $relativePath = 'assets/images/kyc_docs/' . basename($photoPath);

                    // Update/create
                    KYCData::updateOrCreate(
                        ['user_id' => $user->id, 'doc_type' => $key],
                        ['document' => $relativePath]
                    );
                }
            }

            // Commit the transaction
            DB::commit();

            // Send account registration email
            $this->mailService->sendAccRegEmail($user, $password);

            return response()->json([
                'success' => 'Employee created successfully.',
            ]);
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();

            return response()->json([
                'error' => 'Failed to create employee. Please try again later.',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        try {
            if ($request->ajax()) {
                // Step 1: Define filters with default values
                $filters = [
                    'created_by' => $id,
                    'created_at' => $request->get('dates') ?? now()->format('Y'), // Default to current year
                ];

                // Step 2: Initialize the sales query
                $salesQuery = Sale::query();

                // Step 3: Apply dynamic filters using the 'when' method
                foreach ($filters as $key => $value) {
                    $salesQuery->when(!empty($value), function ($query) use ($key, $value) {
                        if ($key === 'created_at') {
                            // Filter by year (current year by default)
                            $query->whereYear('created_at', Carbon::parse($value)->year);
                        } else {
                            $query->where($key, $value);
                        }
                    });
                }

                // Step 4: Fetch sales IDs for detailed calculations
                $salesIds = $salesQuery->pluck('id');

                // Step 5: Calculate commission statistics
                $allTimeCommission = Commission::whereIn('product_id', SaleItem::whereIn('sale_id', $salesIds)->pluck('product_id'))
                    ->sum('commission_amount'); // Total commission for all relevant sales

                $commissionPaid = Commission::whereIn('product_id', SaleItem::whereIn('sale_id', $salesIds)->pluck('product_id'))
                    ->where('status', 'paid') // Assuming a `status` field tracks payment
                    ->sum('commission_amount'); // Total paid commission

                $commissionPending = $allTimeCommission - $commissionPaid; // Remaining unpaid commission

                // Step 6: Calculate the number of sales
                $salesCount = $salesQuery->count(); // Total number of sales based on the filters

                // Step 7: Prepare helper function for card data
                $progress = fn($value, $total) => $total > 0 ? round(($value / $total) * 100) : 0;
                $createCard = fn($icon, $bg, $value, $progress) => compact('icon', 'bg', 'value', 'progress');

                // Step 8: Prepare dashboard cards
                $cards = [
                    'no of sales' => $createCard(
                        'bi-cart',
                        'info',
                        number_format($salesCount),
                        100 // Progress is always 100% for total count
                    ),
                    'all commission' => $createCard(
                        'bi-graph-up',
                        'primary',
                        'Ksh ' . number_format($allTimeCommission, 2),
                        100 // Always 100% for "all time"
                    ),
                    'paid commission' => $createCard(
                        'bi-cash',
                        'success',
                        'Ksh ' . number_format($commissionPaid, 2),
                        $progress($commissionPaid, $allTimeCommission)
                    ),
                    'unpaid commission' => $createCard(
                        'bi-clock',
                        'warning',
                        'Ksh ' . number_format($commissionPending, 2),
                        $progress($commissionPending, $allTimeCommission)
                    ),
                ];

                // Step 10: Prepare the response
                $responseData = [
                    'cards' => $cards
                ];

                // Return the JSON response
                return response()->json($responseData, 200);
            }

            // Fetch User with related data (branch and permissions)
            $user = User::with(['branch', 'permissions', 'kyc', 'commissions'])->findOrFail($id);

            return view('portal.employee.show', compact('user'));
        } catch (\Exception $exception) {
            // Log the exception details
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());

            // Return a general error message
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    public function edit(string $id)
    {
        try {
            // Fetch User with related data (branch and permissions)
            $user = User::with(['branch', 'permissions'])->findOrFail($id);

            // Return the main view with user data and other necessary data
            return view('portal.employee.edit', compact('user'));
        } catch (\Exception $exception) {
            // Log the exception details
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            // Return a general error message
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        // Validate the input data
        $validatedData = $request->validate([
            'branch' => 'required|exists:branches,id',
            'user_name' => 'required|string|unique:users,user_name,' . $id,
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:15|unique:users,phone,' . $id,
            'id_number' => 'required|string|max:20|unique:users,id_number,' . $id,
            'commission_rate' => 'required',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'viewable_branches' => 'nullable|array',
            'viewable_branches.*' => 'exists:branches,id',
            'viewable_catalogues' => 'nullable|array',
            'viewable_catalogues.*' => 'exists:catalogues,id',
            'viewable_products' => 'nullable|array',
            'viewable_products.*' => 'exists:products,id',
        ]);

        // Get the user to be updated
        $user = User::findOrFail($id);

        // Use a database transaction
        DB::beginTransaction();

        try {
            // Update the user data
            $user->update([
                'branch_id' => $validatedData['branch'],
                'user_name' => $validatedData['user_name'],
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'],
                'id_number' => $validatedData['id_number'],
                'commission_rate' => $validatedData['commission_rate'],
                'updated_by' => Auth::user()->id,
            ]);

            // Sync permissions
            if (!empty($validatedData['permissions'])) {
                $user->permissions()->sync([]);
                foreach ($validatedData['permissions'] as $permissionId) {
                    $permissionData = [
                        'permission_id' => $permissionId,
                        'selected_branches' => $permissionId == Permission::where('slug', 'manager_branch')->value('id')
                            ? json_encode($validatedData['viewable_branches'] ?? [])
                            : null,
                        'selected_products' => $permissionId == Permission::where('slug', 'product_view')->value('id')
                            ? json_encode($validatedData['viewable_products'] ?? [])
                            : null,
                        'selected_catalogues' => $permissionId == Permission::where('slug', 'catalogue_view')->value('id')
                            ? json_encode($validatedData['viewable_catalogues'] ?? [])
                            : null,
                    ];

                    $user->permissions()->attach($permissionId, $permissionData);
                }
            }

            // Add/update any uploaded photo
            $kyc_docs = kyc_docs();

            // Validate the request data for images dynamically
            foreach ($kyc_docs as $key => $value) {
                $validationRules[$key] = 'nullable|mimes:jpeg,png,jpg,pdf';
            }

            $request->validate($validationRules);

            // Store each uploaded photo
            foreach ($kyc_docs as $key => $value) {
                if ($request->hasFile($key)) {
                    // Generate a unique file name
                    $fileName = $key . '_' . time() . '.' . $request->file($key)->getClientOriginalExtension();
                    // Move the uploaded file to the public/assets/images/kyc_docs directory
                    $photoPath = $request->file($key)->move(public_path('assets/images/kyc_docs'), $fileName);
                    $relativePath = 'assets/images/kyc_docs/' . basename($photoPath);

                    // Update/create
                    KYCData::updateOrCreate(
                        ['user_id' => $user->id, 'doc_type' => $key],
                        ['document' => $relativePath]
                    );
                }
            }

            // Commit the transaction
            DB::commit();

            return response()->json([
                'success' => 'Employee updated successfully.',
            ]);
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();

            return response()->json([
                'error' => 'Failed to update employee.' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request, string $id)
    {
        try {
            // Validate the request
            $request->validate([
                'password' => 'required|string',
            ]);

            // Verify the user's password
            if (!Hash::check($request->password, Auth::user()->password)) {
                return response()->json(['error' => 'The password you entered is incorrect.'], 403);
            }

            // Find the user by key
            $user = User::findOrFail($id);

            if (!$user) {
                return response()->json(['error' => 'user not found.'], 404);
            }

            // Delete the user
            $user->delete();

            return response()->json(['success' => 'Employee has been deleted successfully.'], 200);
        } catch (\Exception $exception) {
            // Log the exception details
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            // Return a general error message
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    public function handleKyc(Request $request, $id)
    {
        // Retrieve the KYC record
        $kyc = KYCData::findOrFail($id);
        
        // Determine action
        $action = $request->input('action');
        if ($action === 'approve') {
            $kyc->status = 'approved';
            $message = 'KYC has been approved successfully.';
        } elseif ($action === 'reject') {
            $kyc->status = 'rejected';
            $message = 'KYC has been rejected successfully.';
        } else {
            return response()->json(['error' => 'Invalid action.'], 400);
        }

        // Save the updated status
        $kyc->save();

        return response()->json(['message' => $message]);
    }
}
