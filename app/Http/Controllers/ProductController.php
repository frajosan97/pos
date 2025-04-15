<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Picqer\Barcode\BarcodeGeneratorPNG;
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
                $filters = array_filter([
                    'created_by'   => $request->get('employee'),
                    'branch_id'    => $request->get('branch'),
                    'catalogue_id' => $request->get('catalogue'),
                    'status'       => 'active',
                ]);
            
                $productsQuery = Products::with(['catalogue', 'branch'])
                    ->select('products.*')
                    ->when($request->has('with_trashed'), function ($query) {
                        $query->withTrashed();
                    });
            
                if (Auth::user()->hasPermission('product.verify')) {
                    $productsQuery->where('is_verified', true);
                }
            
                foreach ($filters as $key => $value) {
                    $productsQuery->where($key, $value);
                }
            
                $products = $productsQuery->get()->map(function ($product) {
                    return [
                        'image' => $this->getProductImage($product),
                        'name' => ucwords($product->name),
                        'catalogue' => optional($product->catalogue)->name ?? 'N/A',
                        'branch' => optional($product->branch)->name ?? 'N/A',
                        'price_list' => '<span>
                                            Normal: ' . number_format($product->normal_price, 2) . '
                                            Whole Sale: ' . number_format($product->whole_sale_price, 2) . '
                                            Agent: ' . number_format($product->agent_price, 2) . '
                                        </span>',
                        'quantity'=>$product->quantity,
                        'status' => $this->getVerificationBadge($product).' '.$this->getStockBalanceBadge($product),
                        'action' => view('portal.product.partials.actions', compact('product'))->render(),
                        'created_at' => $product->created_at->format('Y-m-d H:i:s'),
                    ];
                });
            
                return DataTables::of($products)
                    ->rawColumns(['image', 'status', 'verification', 'stock_balance', 'action', 'price_list'])
                    ->make(true);
            }

            return view('portal.product.index');
        } catch (\Exception $exception) {
            Log::error('Product listing error: '.$exception->getMessage());
            return response()->json(['error' => 'Failed to retrieve products'], 500);
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
        $validated = $request->validate([
            'catalogue_id' => 'required|exists:catalogues,id',
            'branch_id' => 'required|exists:branches,id',
            'barcode' => 'required|string|max:255|unique:products,barcode',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'buying_price' => 'required|numeric|min:0',
            'normal_price' => 'required|numeric|gte:buying_price',
            'whole_sale_price' => 'required|numeric|gte:buying_price',
            'agent_price' => 'required|numeric|gte:buying_price',
            'quantity' => 'required|integer|min:0',
            'sku' => 'required|string|max:255|unique:products,sku',
            'commission_on_sale' => 'required|numeric|min:0|max:100',
            'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'unit' => 'nullable|string|max:20',
            'weight' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullabke|numeric|min:0|max:100',
            'status' => 'nullable|in:active,inactive',
        ]);

        try {
            $productData = [
                ...$validated,
                'photo' => $this->handleImageUpload($request),
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ];

            $product = Products::create($productData);

            // Log product creation
            Log::info('Product created', [
                'product_id' => $product->id,
                'by_user' => Auth::id(),
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => 'Product created successfully',
                'needs_verification' => !$product->is_verified
            ], 201);

        } catch (\Exception $exception) {
            Log::error('Product creation failed: '.$exception->getMessage());
            return response()->json(['error' => 'Failed to create product'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $product = Products::with(['catalogue', 'branch'])
                ->when(Gate::allows('view-deleted-products'), function($query) {
                    $query->withTrashed();
                })
                ->findOrFail($id);

            $generator = new BarcodeGeneratorPNG();
            $barcode = base64_encode($generator->getBarcode($product->barcode, $generator::TYPE_CODE_128));

            return view('portal.product.show', compact('product', 'barcode'));
        } catch (\Exception $exception) {
            Log::error('Product not found: '.$exception->getMessage());
            return back()->with('error', 'Product not found');
        }
    }

    /**
     * Verify a product
     */
    public function verify(Request $request, $id)
    {
        try {
            $product = Products::findOrFail($id);

            $product->update([
                'is_verified' => true,
                'verified_by' => Auth::user()->name,
                'verified_at' => now(),
            ]);

            Log::info('Product verified', [
                'product_id' => $id,
                'by_user' => Auth::id()
            ]);

            return response()->json(['success' => 'Product verified successfully']);
        } catch (\Exception $exception) {
            Log::error('Product verification failed: '.$exception->getMessage());
            return response()->json(['error' => 'Verification failed'], 500);
        }
    }

    /**
     * Soft delete a product
     */
    public function destroy($id)
    {
        try {
            $product = Products::findOrFail($id);
            
            Gate::authorize('delete', $product);

            $product->delete();

            Log::info('Product soft deleted', [
                'product_id' => $id,
                'by_user' => Auth::id()
            ]);

            return response()->json(['success' => 'Product archived successfully']);
        } catch (\Exception $exception) {
            Log::error('Product deletion failed: '.$exception->getMessage());
            return response()->json(['error' => 'Deletion failed'], 500);
        }
    }

    /**
     * Restore a soft deleted product
     */
    public function restore($id)
    {
        try {
            $product = Products::withTrashed()->findOrFail($id);
            
            Gate::authorize('restore', $product);

            $product->restore();

            Log::info('Product restored', [
                'product_id' => $id,
                'by_user' => Auth::id()
            ]);

            return response()->json(['success' => 'Product restored successfully']);
        } catch (\Exception $exception) {
            Log::error('Product restoration failed: '.$exception->getMessage());
            return response()->json(['error' => 'Restoration failed'], 500);
        }
    }

    /**
     * Permanently delete a product
     */
    public function forceDelete($id)
    {
        try {
            $product = Products::withTrashed()->findOrFail($id);
            
            Gate::authorize('forceDelete', $product);

            // Delete associated image if exists
            if ($product->photo && file_exists(public_path($product->photo))) {
                unlink(public_path($product->photo));
            }

            $product->forceDelete();

            Log::warning('Product permanently deleted', [
                'product_id' => $id,
                'by_user' => Auth::id()
            ]);

            return response()->json(['success' => 'Product permanently deleted']);
        } catch (\Exception $exception) {
            Log::error('Product permanent deletion failed: '.$exception->getMessage());
            return response()->json(['error' => 'Permanent deletion failed'], 500);
        }
    }

    /**
     * Handle product image upload
     */
    protected function handleImageUpload(Request $request): ?string
    {
        if (!$request->hasFile('product_image')) {
            return null;
        }

        $file = $request->file('product_image');
        $fileName = uniqid().'_'.str_replace(' ', '_', $file->getClientOriginalName());
        $destinationPath = public_path('assets/images/products');
        
        $file->move($destinationPath, $fileName);
        
        return 'assets/images/products/'.$fileName;
    }

    /**
     * Get product image HTML
     */
    protected function getProductImage(Products $product): string
    {
        $imagePath = $product->photo ?? '/assets/images/defaults/product.png';
        $altText = $product->photo 
            ? htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8') 
            : 'No Image';
            
        return '<img src="'.asset($imagePath).'" alt="'.$altText.'" width="50" height="50" class="rounded">';
    }

    /**
     * Get status badge HTML
     */
    protected function getStatusBadge(Products $product): string
    {
        return $product->status == 'active'
            ? '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Active</span>'
            : '<span class="badge bg-danger"><i class="bi bi-x-circle"></i> Inactive</span>';
    }

    protected function getStockBalanceBadge(Products $product): string
    {
        return $product->isLowStock
            ? '<span class="badge bg-danger"><i class="bi bi-x-circle"></i> Low Stock</span>'
            : '';
    }

    /**
     * Get verification badge HTML
     */
    protected function getVerificationBadge(Products $product): string
    {
        if ($product->is_verified) {
            return '<span class="badge bg-success" title="Verified by '.$product->verified_by.' at '.date('Y-m-d',$product->verified_at).'">
                <i class="bi bi-check-circle"></i> Verified
            </span>';
        }
        
        return '<span class="badge bg-warning text-dark">
            <i class="bi bi-exclamation-triangle"></i> Unverified
        </span>';
    }
}