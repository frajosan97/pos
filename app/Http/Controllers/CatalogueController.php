<?php

namespace App\Http\Controllers;

use App\Models\Catalogue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
                            'created_at' => $catalogue->created_at->format('Y-m-d H:i:s'),
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
            'branch_id' => 'required|string|max:255',
        ]);

        try {
            // Create a new catalogue
            $catalogue = new Catalogue();
            $catalogue->branch_id = $request->branch_id;
            $catalogue->name = $request->catalogue;
            $catalogue->save();

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
            $catalogue->name = $request->catalogue;  // Capitalize the name
            $catalogue->save();

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
    public function destroy(string $id)
    {
        try {
            // Fetch and delete the catalogue
            $catalogue = Catalogue::findOrFail($id);
            $catalogue->delete();

            // Return success response
            return response()->json('Catalogue deleted successfully.', 200);
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }
}
