<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Sale;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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
                $users = User::with(['branch', 'role'])
                    ->get()
                    ->map(function ($user) {
                        return [
                            'name' => ucwords($user->name),
                            'email' => $user->email,
                            'phone' => $user->phone,
                            'role' => ucwords($user->role->name),
                            'branch' => ucwords($user->branch->name),
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
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => 'An error occurred while fetching employees.'], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('portal.employee.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'branch' => 'required|exists:branches,id', // Ensure branch exists in the DB
                'role' => 'required|exists:roles,id', // Ensure role exists in the DB
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'phone' => 'required|string|max:15|regex:/^(\+?[\d\s\-()]){10,15}$/', // Improve phone validation with regex
            ]);

            $user = User::create([
                'branch_id' => $request->branch,
                'role_id' => $request->role,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make('12345678'),
                'created_by' => Auth::user()->id ?? null,
                'updated_by' => Auth::user()->id ?? null,
            ]);

            // Send account registration email
            $this->mailService->sendAccRegEmail($user);

            // Success response
            return response()->json(['success' => 'Employee registered successfully and activation email sent to the provided email address']);
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => 'An error occurred while saving the employee.'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        try {
            // Fetch User with related data (branch and role)
            $user = User::with(['branch', 'role'])->findOrFail($id);

            // If the request is AJAX, fetch and return sales data for the user
            if ($request->ajax()) {
                // Fetch sales data related to the user, with saleItems and customer
                $salesData = Sale::where('created_by', $user->id)
                    ->with(['customer']) // Eager load customer relation (no need to load saleItems unless needed)
                    ->get()
                    ->map(function ($sale) {
                        $customerName = ucwords($sale->customer->name ?? 'No customer attached'); // Use the customer relation
                        $statusBtn = ($sale->status == 'paid')
                            ? '<strong class="text-success text-capitalize"><i class="fas fa-check-circle"></i> ' . $sale->status . '</strong>'
                            : '<strong class="text-danger text-capitalize"><i class="fas fa-times-circle"></i> ' . $sale->status . '</strong>';

                        return [
                            'sale_date' => $sale->created_at->format('Y-m-d H:i:s'), // Format sale date
                            'customer_name' => $customerName,
                            'amount' => '<div class="text-end">' . number_format($sale->total_amount, 2) . '</div>', // Format amount
                            'status' => $statusBtn, // Sale status
                            'action' => view('portal.sale.partials.sale_actions', compact('sale'))->render(), // View with action buttons
                        ];
                    });

                // Return sales data to DataTables
                return DataTables::of($salesData)
                    ->rawColumns(['amount', 'status', 'action'])
                    ->make(true);
            }

            // Return the main view with user data if it's not an AJAX request
            return view('portal.employee.show', compact('user'));
        } catch (\Exception $exception) {
            // Log the error details for debugging
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());

            // Return a generic error message as JSON
            return response()->json(['error' => 'An error occurred while fetching the employee details.'], 500);
        }
    }

    public function edit(string $id)
    {
        try {
            // Fetch User with related data (branch and role)
            $user = User::with(['branch', 'role'])->findOrFail($id);

            // Return the main view with user data if it's not an AJAX request
            return view('portal.employee.edit', compact('user'));
        } catch (\Exception $exception) {
            // Log the error details for debugging
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());

            // Return a generic error message as JSON
            return response()->json(['error' => 'An error occurred while fetching the employee details.'], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            // Validate request data
            $request->validate([
                'user_name' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'gender' => 'required|in:Male,Female',
                'phone' => 'required|string|max:15',
                'branch_id' => 'required|exists:branches,id',
                'role_id' => 'required|exists:roles,id',
                'status' => 'required|boolean',
                'id_number' => 'nullable|string|max:20',
                'passport' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $user = User::findOrFail($id);

            // Handle the product image upload if present
            $passportPath = null;
            if ($request->hasFile('passport')) {
                $file = $request->file('passport');
                $destinationPath = public_path('assets/images/profiles');
                // Generate a unique name for the image
                $fileName = uniqid() . '_' . $file->getClientOriginalName();
                // Move the file to the desired location
                $file->move($destinationPath, $fileName);
                // Save the relative path
                $passportPath = 'assets/images/profiles/' . $fileName;
            }

            // Update the user name
            $user->update([
                'user_name' => $request->input('user_name'),
                'name' => $request->input('name'),
                'gender' => $request->input('gender'),
                'phone' => $request->input('phone'),
                'branch_id' => $request->input('branch_id'),
                'role_id' => $request->input('role_id'),
                'status' => $request->input('status'),
                'id_number' => $request->input('id_number'),
                'passport' => $passportPath ?? $user->passport,
            ]);

            // Return a success message
            return response()->json(['success' => 'E,ployee profile updated successfully'], 200);
        } catch (\Exception $exception) {
            // Log the exception details
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            // Return a general error message
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }
}
