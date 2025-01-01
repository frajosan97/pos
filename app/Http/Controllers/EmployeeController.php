<?php

namespace App\Http\Controllers;

use App\Models\Permission;
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
                $users = User::with(['branch'])
                    ->get()
                    ->map(function ($user) {
                        return [
                            'name' => ucwords($user->name),
                            'email' => $user->email,
                            'phone' => $user->phone,
                            'branch' => ucwords(optional($user->branch)->name),
                            'status' => $user->email_verified_at ? 'Active' : 'Inactive',
                            'action' => view('portal.employee.partials.actions', compact('user'))->render(),
                            'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                        ];
                    });

                return DataTables::of($users)->make(true);
            } else {
                return view('portal.employee.index');
            }
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
            // Fetch User with related data (branch and permissions)
            $user = User::with(['branch', 'permissions'])->findOrFail($id);

            // Calculate total commission (all-time commission)
            $allTimeCommission = $user->commissions()->sum('commission_amount');

            // Calculate month-to-date commission
            $currentMonth = now()->month;
            $monthToDateCommission = $user->commissions()
                ->whereMonth('created_at', $currentMonth)
                ->sum('commission_amount');

            // Return the main view with user data and commissions if it's not an AJAX request
            return view('portal.employee.show', compact('user', 'allTimeCommission', 'monthToDateCommission'));
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

            // Commit the transaction
            DB::commit();

            return response()->json([
                'success' => 'Employee updated successfully.',
            ]);
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();

            return response()->json([
                'error' => 'Failed to update employee. Please try again later.',
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
}
