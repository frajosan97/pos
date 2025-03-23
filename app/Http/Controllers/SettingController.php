<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Constituency;
use App\Models\County;
use App\Models\Location;
use App\Models\Role;
use App\Models\Ward;
use App\Models\Company;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Artisan;

class SettingController extends Controller
{
    public function county(Request $request)
    {
        try {
            if ($request->ajax()) {
                $countyData = County::all()
                    ->map(function ($county) {
                        return [
                            'county' => ucwords($county->name),
                            'action' => view('portal.setting.partials.county_actions', compact('county'))->render(),
                        ];
                    });

                return DataTables::of($countyData)
                    ->rawColumns(['county', 'action'])
                    ->make(true);
            } else {
                return view('portal.setting.county');
            }
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => 'An error occurred while processing the request. Please try again.'], 500);
        }
    }

    public function storeCounty(Request $request)
    {
        try {
            // Validate the request data
            $request->validate([
                'county' => 'required|string|max:255',
            ]);

            // Create the county
            $county = County::create([
                'name' => $request->input('county'),
            ]);

            // Check if county was created successfully
            if ($county) {
                return response()->json(['success' => 'County created successfully'], 200);
            }

            // If county creation fails, return an error message
            return response()->json(['error' => 'Failed to create county.'], 500);
        } catch (\Exception $exception) {
            // Log the exception details
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            // Return a general error message
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    public function updateCounty(Request $request, string $id)
    {
        try {
            // Validate the request data
            $request->validate([
                'county' => 'required|string|max:255',
            ]);

            // Find the county by ID or fail if not found
            $county = County::findOrFail($id);

            // Update the county name
            $county->update([
                'name' => $request->input('county'),
            ]);

            // Return a success message
            return response()->json(['success' => 'County updated successfully'], 200);
        } catch (\Exception $exception) {
            // Log the exception details
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            // Return a general error message
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    public function constituency(Request $request)
    {
        try {
            if ($request->ajax()) {
                // Fetch all constituencies with their related county
                $constituencyData = Constituency::with('county')->get()
                    ->map(function ($constituency) {
                        return [
                            'county' => ucwords($constituency->county->name),
                            'constituency' => ucwords($constituency->name),
                            'action' => view('portal.setting.partials.constituency_actions', compact('constituency'))->render(),
                        ];
                    });

                // Return data formatted for DataTables
                return DataTables::of($constituencyData)
                    ->rawColumns(['action']) // Only 'action' column needs raw rendering
                    ->make(true);
            }

            // Render the view if the request is not AJAX
            return view('portal.setting.constituency');
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => 'An error occurred while processing the request. Please try again.'], 500);
        }
    }

    public function storeConstituency(Request $request)
    {
        try {
            // Validate the request data
            $request->validate([
                'county' => 'required|string|max:255',
                'constituency' => 'required|string|max:255',
            ]);

            // Create the constituency
            $constituency = Constituency::create([
                'county_id' => $request->input('county'),
                'name' => $request->input('constituency'),
            ]);

            // Check if constituency was created successfully
            if ($constituency) {
                return response()->json(['success' => 'Constituency created successfully'], 200);
            }

            // If constituency creation fails, return an error message
            return response()->json(['error' => 'Failed to create constituency.'], 500);
        } catch (\Exception $exception) {
            // Log the exception details
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            // Return a general error message
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    public function updateConstituency(Request $request, string $id)
    {
        try {
            // Validate the request data
            $request->validate([
                'county' => 'required|string|max:255',
                'constituency' => 'required|string|max:255',
            ]);

            // Find the constituency by ID or fail if not found
            $constituency = Constituency::findOrFail($id);

            // Update the constituency name
            $constituency->update([
                'county_id' => $request->input('county'),
                'name' => $request->input('constituency'),
            ]);

            // Return a success message
            return response()->json(['success' => 'constituency updated successfully'], 200);
        } catch (\Exception $exception) {
            // Log the exception details
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            // Return a general error message
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    public function ward(Request $request)
    {
        try {
            if ($request->ajax()) {
                // Fetch all constituencies with their related county
                $wardData = Ward::with(['county', 'constituency'])->get()
                    ->map(function ($ward) {
                        return [
                            'county' => ucwords($ward->county->name),
                            'constituency' => ucwords($ward->constituency->name),
                            'ward' => ucwords($ward->name),
                            'action' => view('portal.setting.partials.ward_actions', compact('ward'))->render(),
                        ];
                    });

                // Return data formatted for DataTables
                return DataTables::of($wardData)
                    ->rawColumns(['action']) // Only 'action' column needs raw rendering
                    ->make(true);
            }

            // Render the view if the request is not AJAX
            return view('portal.setting.ward');
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => 'An error occurred while processing the request. Please try again.'], 500);
        }
    }

    public function storeWard(Request $request)
    {
        try {
            // Validate the request data
            $request->validate([
                'county' => 'required|string|max:255',
                'constituency' => 'required|string|max:255',
                'ward' => 'required|string|max:255',
            ]);

            // Create the ward
            $ward = Ward::create([
                'county_id' => $request->input('county'),
                'constituency_id' => $request->input('constituency'),
                'name' => $request->input('ward'),
            ]);

            // Check if ward was created successfully
            if ($ward) {
                return response()->json(['success' => 'ward created successfully'], 200);
            }

            // If ward creation fails, return an error message
            return response()->json(['error' => 'Failed to create ward.'], 500);
        } catch (\Exception $exception) {
            // Log the exception details
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            // Return a general error message
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    public function updateWard(Request $request, string $id)
    {
        try {
            // Validate the request data
            $request->validate([
                'county' => 'required|string|max:255',
                'constituency' => 'required|string|max:255',
                'ward' => 'required|string|max:255',
            ]);

            // Find the ward by ID or fail if not found
            $ward = Ward::findOrFail($id);

            // Update the ward name
            $ward->update([
                'county_id' => $request->input('county'),
                'constituency_id' => $request->input('constituency'),
                'name' => $request->input('ward'),
            ]);

            // Return a success message
            return response()->json(['success' => 'ward updated successfully'], 200);
        } catch (\Exception $exception) {
            // Log the exception details
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            // Return a general error message
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    public function location(Request $request)
    {
        try {
            if ($request->ajax()) {
                // Fetch all constituencies with their related county
                $locationData = Location::with(['county', 'constituency', 'ward'])->get()
                    ->map(function ($location) {
                        return [
                            'county' => ucwords($location->county->name),
                            'constituency' => ucwords($location->constituency->name),
                            'ward' => ucwords($location->ward->name),
                            'location' => ucwords($location->name),
                            'action' => view('portal.setting.partials.location_actions', compact('location'))->render(),
                        ];
                    });

                // Return data formatted for DataTables
                return DataTables::of($locationData)
                    ->rawColumns(['action']) // Only 'action' column needs raw rendering
                    ->make(true);
            }

            // Render the view if the request is not AJAX
            return view('portal.setting.location');
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => 'An error occurred while processing the request. Please try again.'], 500);
        }
    }

    public function storeLocation(Request $request)
    {
        try {
            // Validate the request data
            $request->validate([
                'county' => 'required|string|max:255',
                'constituency' => 'required|string|max:255',
                'ward' => 'required|string|max:255',
                'location' => 'required|string|max:255',
            ]);

            // Create the location
            $location = Location::create([
                'county_id' => $request->input('county'),
                'constituency_id' => $request->input('constituency'),
                'ward_id' => $request->input('ward'),
                'name' => $request->input('location'),
            ]);

            // Check if location was created successfully
            if ($location) {
                return response()->json(['success' => 'location created successfully'], 200);
            }

            // If location creation fails, return an error message
            return response()->json(['error' => 'Failed to create location.'], 500);
        } catch (\Exception $exception) {
            // Log the exception details
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            // Return a general error message
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    public function updateLocation(Request $request, string $id)
    {
        try {
            // Validate the request data
            $request->validate([
                'county' => 'required|string|max:255',
                'constituency' => 'required|string|max:255',
                'ward' => 'required|string|max:255',
                'location' => 'required|string|max:255',
            ]);

            // Find the location by ID or fail if not found
            $location = Location::findOrFail($id);

            // Update the location name
            $location->update([
                'county_id' => $request->input('county'),
                'constituency_id' => $request->input('constituency'),
                'ward_id' => $request->input('ward'),
                'name' => $request->input('location'),
            ]);

            // Return a success message
            return response()->json(['success' => 'location updated successfully'], 200);
        } catch (\Exception $exception) {
            // Log the exception details
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            // Return a general error message
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    public function branch(Request $request)
    {
        try {
            if ($request->ajax()) {
                // Fetch all constituencies with their related county
                $branchData = Branch::with(['county', 'constituency', 'ward'])->get()
                    ->map(function ($branch) {
                        return [
                            'county' => ucwords($branch->county->name),
                            'constituency' => $branch->constituency ? ucwords($branch->constituency->name) : 'Not set',
                            'ward' => $branch->ward ? ucwords($branch->ward->name) : 'Not set',
                            'location' => $branch->location ? ucwords($branch->location->name) : 'Not set',
                            'branch' => ucwords($branch->name),
                            'action' => view('portal.setting.partials.branch_actions', compact('branch'))->render(),
                        ];
                    });

                // Return data formatted for DataTables
                return DataTables::of($branchData)
                    ->rawColumns(['action']) // Only 'action' column needs raw rendering
                    ->make(true);
            }

            // Render the view if the request is not AJAX
            return view('portal.setting.branch');
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => 'An error occurred while processing the request. Please try again.'], 500);
        }
    }

    public function storeBranch(Request $request)
    {
        try {
            // Validate the request data
            $request->validate([
                'county' => 'required|string|max:255',
                'branch' => 'required|string|max:255',
            ]);

            // Create the branch
            $branch = Branch::create([
                'company_id' => 1,
                'county_id' => $request->input('county'),
                'constituency_id' => $request->input('constituency'),
                'ward_id' => $request->input('ward'),
                'location_id' => $request->input('location'),
                'name' => $request->input('branch'),
            ]);

            // Check if branch was created successfully
            if ($branch) {
                return response()->json(['success' => 'branch created successfully'], 200);
            }

            // If branch creation fails, return an error message
            return response()->json(['error' => 'Failed to create branch.'], 500);
        } catch (\Exception $exception) {
            // Log the exception details
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            // Return a general error message
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    public function updateBranch(Request $request, string $id)
    {
        try {
            // Validate the request data
            $request->validate([
                'county' => 'required|string|max:255',
                'branch' => 'required|string|max:255',
            ]);

            // Find the branch by ID or fail if not found
            $branch = Branch::findOrFail($id);

            // Update the branch name
            $branch->update([
                'county_id' => $request->input('county'),
                'constituency_id' => $request->input('constituency'),
                'ward_id' => $request->input('ward'),
                'location_id' => $request->input('location'),
                'name' => $request->input('branch'),
            ]);

            // Return a success message
            return response()->json(['success' => 'branch updated successfully'], 200);
        } catch (\Exception $exception) {
            // Log the exception details
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            // Return a general error message
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    public function role(Request $request)
    {
        try {
            if ($request->ajax()) {
                $roleData = Role::all()
                    ->map(function ($role) {
                        return [
                            'name' => ucwords($role->name),
                            'description' => ucwords($role->description),
                            'action' => view('portal.setting.partials.role_actions', compact('role'))->render(),
                        ];
                    });

                return DataTables::of($roleData)
                    ->rawColumns(['role', 'action'])
                    ->make(true);
            } else {
                return view('portal.setting.role');
            }
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => 'An error occurred while processing the request. Please try again.'], 500);
        }
    }

    public function showRole(string $id)
    {
        $role = Role::with('permissions')->find($id);
        $all_permissions = Permission::all();
        return view('portal.setting.role_show', compact(['role', 'all_permissions']));
    }

    public function storeRole(Request $request)
    {
        try {
            // Validate the request data
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string|max:255',
            ]);

            // Create the role
            $role = Role::create([
                'slug' => my_slug($request->input('name')),
                'name' => $request->input('name'),
                'description' => $request->input('description'),
            ]);

            // Check if role was created successfully
            if ($role) {
                return response()->json(['success' => 'Role created successfully'], 200);
            }

            // If role creation fails, return an error message
            return response()->json(['error' => 'Failed to create role.'], 500);
        } catch (\Exception $exception) {
            // Log the exception details
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            // Return a general error message
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    public function updateRole(Request $request, string $id)
    {
        try {
            // Validate the request data
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string|max:255',
            ]);

            // Find the role by ID or fail if not found
            $role = Role::findOrFail($id);

            // Update the role name
            $role->update([
                'slug' => my_slug($request->input('name')),
                'name' => $request->input('name'),
                'description' => $request->input('description'),
            ]);

            // Return a success message
            return response()->json(['success' => 'Role updated successfully'], 200);
        } catch (\Exception $exception) {
            // Log the exception details
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            // Return a general error message
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    public function updatePermission(Request $request, string $id)
    {
        try {
            // Validate the request data
            $request->validate([
                'permissions' => 'array|nullable|exists:permissions,id', // Ensure 'permissions' is an array of valid IDs or null
            ]);

            // Find the role by ID or fail if not found
            $role = Role::findOrFail($id);

            // Sync the permissions for the role
            // This will add the new permissions and remove any not in the array
            $role->permissions()->sync($request->permissions ?? []);

            // Return a success message
            return response()->json(['success' => 'Permissions updated successfully'], 200);
        } catch (\Exception $exception) {
            // Log the exception details
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());

            // Return a general error message
            return response()->json(['error' => 'An error occurred while updating permissions.'], 500);
        }
    }

    public function company(Request $request)
    {
        $company = Company::first();
        return view('portal.setting.company', compact('company'));
    }

    public function updateCompany(Request $request, string $id)
    {
        try {
            // Validate incoming data
            $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'phone' => 'required|string|max:15',
                'email' => 'required|email|max:255',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'color' => 'nullable|string|max:7',
                'commission_by' => 'required|max:255',
                // 'sms_mode' => 'required|in:online,offline',
                // 'sms_partner_id' => 'nullable|string|max:255',
                // 'sms_api_key' => 'nullable|string|max:255',
                // 'sms_sender_id' => 'nullable|string|max:255',
                // 'sms_api_url' => 'nullable|string|max:255',
            ]);

            // Find the company record
            $company = Company::findOrFail($id);

            // Handle the product image upload if present
            $logoPath = null;
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $destinationPath = public_path('assets/images/logo');
                // Generate a unique name for the image
                $fileName = uniqid() . '_' . $file->getClientOriginalName();
                // Move the file to the desired location
                $file->move($destinationPath, $fileName);
                // Save the relative path
                $logoPath = $fileName;
            }

            // Update the company name
            $company->update([
                'name' => $request->input('name'),
                'address' => $request->input('address'),
                'phone' => $request->input('phone'),
                'email' => $request->input('email'),
                'color' => $request->input('color'),
                'commission_by' => $request->input('commission_by'),
                'logo' => $logoPath ?? $company->logo,
                // 'sms_mode' => $request->input('sms_mode'),
                // 'sms_partner_id' => $request->input('sms_partner_id'),
                // 'sms_api_key' => $request->input('sms_api_key'),
                // 'sms_sender_id' => $request->input('sms_sender_id'),
                // 'sms_api_url' => $request->input('sms_api_url'),
            ]);

            // Return a success message
            return response()->json(['success' => 'Company profile updated successfully'], 200);
        } catch (\Exception $exception) {
            // Log the exception details
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            // Return a general error message
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    /**
     * Clear and optimize the system cache.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearCache()
    {
        try {
            // Clear application cache
            Artisan::call('cache:clear');
            // Clear route cache
            Artisan::call('route:clear');
            // Clear config cache
            Artisan::call('config:clear');
            // Clear view cache
            Artisan::call('view:clear');

            return response()->json([
                'status' => 'success',
                'message' => 'Application cache cleared successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to clear cache: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Optimize the system.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function optimize()
    {
        try {
            // Optimize the application
            Artisan::call('optimize');

            return response()->json([
                'status' => 'success',
                'message' => 'Application optimized successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to optimize application: ' . $e->getMessage(),
            ]);
        }
    }
}
