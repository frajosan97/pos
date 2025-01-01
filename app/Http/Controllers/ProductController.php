<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                // Get ajax request filters parameters
                $filters = [
                    'created_by' => $request->get('employee'),
                    'branch_id' => $request->get('branch'),
                    'catalogue_id' => $request->get('catalogue'),
                ];

                // Build the query
                $productsQuery = Products::with(['catalogue']);

                // Apply filters dynamically
                foreach ($filters as $key => $value) {
                    if (!empty($value)) {
                        $productsQuery->where($key, $value);
                    }
                }

                // Execute the query
                $products = $productsQuery->get()->map(function ($product) {
                    return [
                        'image' => $product->photo
                            ? '<img src="' . asset($product->photo) . '" alt="' . htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8') . '" width="50" height="50" class="rounded">'
                            : '<img src="' . asset('/assets/images/defaults/product.png') . '" alt="No Image" width="50" height="50" class="rounded">',
                        'name' => ucwords($product->name),
                        'quantity' => ucwords($product->quantity),
                        'normal_price' => number_format($product->normal_price, 2),
                        'whole_sale_price' => number_format($product->whole_sale_price, 2),
                        'agent_price' => number_format($product->agent_price, 2),
                        'stock_balance' => $product->quantity,
                        'status' => $product->status == 'active'
                            ? '<span class="text-nowrap btn btn-success"><i class="bi bi-check-circle"></i> Active</span>'
                            : '<span class="text-nowrap btn btn-danger"><i class="bi bi-x-circle"></i> Inactive</span>',
                        'stock_alert_class' => $product->quantity < $product->threshold
                            ? 'text-danger'
                            : 'text-success',
                        'action' => view('portal.product.partials.actions', compact('product'))->render(),
                        'created_at' => $product->created_at->format('Y-m-d H:i:s'),
                    ];
                });

                // Return the data formatted for DataTables
                return DataTables::of($products)
                    ->rawColumns(['image', 'status', 'stock_alert_class', 'action'])
                    ->make(true);
            }

            // Return the main product page view
            return view('portal.product.index');
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('portal.product.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'catalogue_id' => 'required|exists:catalogues,id',
            'branch_id' => 'required|exists:branches,id',
            'barcode' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'buying_price' => 'required|numeric|min:0',
            'normal_price' => 'required|numeric|min:0',
            'whole_sale_price' => 'required|numeric|min:0',
            'agent_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'sku' => 'required|string|max:255',
            'commission_on_sale' => 'required|numeric|min:0',
            'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            // Handle the product image upload if present
            $imagePath = null;
            if ($request->hasFile('product_image')) {
                $file = $request->file('product_image');
                $destinationPath = public_path('assets/images/products');
                // Generate a unique name for the image
                $fileName = uniqid() . '_' . $file->getClientOriginalName();
                // Move the file to the desired location
                $file->move($destinationPath, $fileName);
                // Save the relative path
                $imagePath = 'assets/images/products/' . $fileName;
            }

            // Retrieve the product by barcode
            $barcode = $request->input('barcode');
            $product = Products::where('barcode', $barcode)->first();

            if ($product) {
                // Update existing product
                $product->update([
                    'branch_id' => $request->input('branch_id'),
                    'catalogue_id' => $request->input('catalogue_id'),
                    'name' => $request->input('name'),
                    'description' => $request->input('description'),
                    'buying_price' => $request->input('buying_price'),
                    'normal_price' => $request->input('normal_price'),
                    'whole_sale_price' => $request->input('whole_sale_price'),
                    'agent_price' => $request->input('agent_price'),
                    'quantity' => $request->input('quantity'),
                    'sku' => $request->input('sku'),
                    'commission_on_sale' => $request->input('commission_on_sale'),
                    'photo' => $imagePath ?? $product->photo,
                    'created_by' => Auth::user()->id
                ]);

                // Success response for update
                return response()->json(['success' => 'Product details updated successfully'], 200);
            } else {
                // Create a new product
                $product = Products::create([
                    'branch_id' => $request->input('branch_id'),
                    'catalogue_id' => $request->input('catalogue_id'),
                    'barcode' => $barcode,
                    'name' => $request->input('name'),
                    'description' => $request->input('description'),
                    'buying_price' => $request->input('buying_price'),
                    'normal_price' => $request->input('normal_price'),
                    'whole_sale_price' => $request->input('whole_sale_price'),
                    'agent_price' => $request->input('agent_price'),
                    'quantity' => $request->input('quantity'),
                    'sku' => $request->input('sku'),
                    'commission_on_sale' => $request->input('commission_on_sale'),
                    'photo' => $imagePath,
                    'updated_by' => Auth::user()->id
                ]);

                // Success response for create
                return response()->json(['success' => 'Product details added successfully'], 200);
            }
        } catch (\Exception $exception) {
            // Log the exception for debugging
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
            // Attempt to find the product
            $product = Products::with('catalogue')->findOrFail($id);

            // Return the view with the product
            return view('portal.product.show', compact('product'));
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
        //
    }
}
