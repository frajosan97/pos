<?php

namespace App\Http\Controllers;

use App\Models\Catalogue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class CatalogueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $catalogues = Catalogue::with('products')  // Eager loading products if needed
                    ->get()
                    ->map(function ($catalogue) {
                        return [
                            'name' => ucwords($catalogue->name),  // Capitalize catalogue name
                            'action' => view('portal.catalogue.partials.actions', compact('catalogue'))->render(),
                        ];
                    });

                // Return the data formatted for DataTables
                return DataTables::of($catalogues)->make(true);
            }

            // Return the main catalogue page view
            return view('portal.catalogue.index');
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            // Attempt to find the catalogue
            $catalogue = Catalogue::findOrFail($id);

            // Return the view with the catalogue
            return view('portal.catalogue.show', compact('catalogue'));
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'catalogue' => 'required|string|max:255',
        ]);

        try {
            // Create a new catalogue
            $catalogue = Catalogue::create([
                'name' => $request->input('catalogue'),
                'created_by' => Auth::user()->id ?? null,
            ]);

            // Return success response
            return response()->json('Catalogue created successfully.', 200);
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validation
        $request->validate([
            'catalogue' => 'required|string|max:255',
        ]);

        try {
            // Fetch the catalogue to update
            $catalogue = Catalogue::findOrFail($id);

            $catalogue->update([
                'name' => $request->input('catalogue'),
                'updated_by' => Auth::user()->id ?? null,
            ]);

            // Return success response
            return response()->json('Catalogue updated successfully.', 200);
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
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

            // Find the catalogue by key
            $catalogue = Catalogue::findOrFail($id);

            if (!$catalogue) {
                return response()->json(['error' => 'catalogue not found.'], 404);
            }

            // Delete the catalogue
            $catalogue->delete();

            return response()->json(['success' => 'Catalogue has been deleted successfully.'], 200);
        } catch (\Exception $exception) {
            // Log the exception details
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            // Return a general error message
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }
}
