<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Helpers\ActivityLogger;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductsExport;
use App\Notifications\ProductAssignedNotification;
use App\Notifications\ProductUnassignedNotification;

class ProductController extends Controller
{
    /**
     * Display a listing of products
     */
    public function index(Request $request)
    {
        $query = Product::with('user');
        
        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title_en', 'like', "%{$search}%")
                  ->orWhere('title_ar', 'like', "%{$search}%")
                  ->orWhere('description_en', 'like', "%{$search}%")
                  ->orWhere('description_ar', 'like', "%{$search}%");
            });
        }
        
        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        
        // Sorting
        $sortField = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);
        
        // Pagination
        $perPage = $request->get('per_page', 15);
        $products = $query->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'data' => $products,
        ], 200);
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title_en' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
            'description_en' => 'required|string',
            'description_ar' => 'required|string',
            'price' => 'required|numeric|min:0',
            'primary_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'other_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'user_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = [
            'title_en' => $request->title_en,
            'title_ar' => $request->title_ar,
            'description_en' => $request->description_en,
            'description_ar' => $request->description_ar,
            'price' => $request->price,
            'slug' => Str::slug($request->title_en),
            'user_id' => $request->user_id,
        ];
        
        // Handle primary image upload
        if ($request->hasFile('primary_image')) {
            $primaryImage = $request->file('primary_image');
            $primaryImageName = time() . '_' . $primaryImage->getClientOriginalName();
            $primaryImage->storeAs('products', $primaryImageName, 'public');
            $data['primary_image'] = 'products/' . $primaryImageName;
        }
        
        // Handle other images upload
        if ($request->hasFile('other_images')) {
            $otherImages = [];
            foreach ($request->file('other_images') as $image) {
                $imageName = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();
                $image->storeAs('products', $imageName, 'public');
                $otherImages[] = 'products/' . $imageName;
            }
            $data['other_images'] = json_encode($otherImages);
        }
        
        $product = Product::create($data);
        
        // Log activity
        ActivityLogger::log('created_product', "Created product: {$product->title_en}", $product);
        
        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product->load('user'),
        ], 201);
    }

    /**
     * Display the specified product
     */
    public function show(string $id)
    {
        $product = Product::with('user')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $product,
        ], 200);
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'title_en' => 'sometimes|required|string|max:255',
            'title_ar' => 'sometimes|required|string|max:255',
            'description_en' => 'sometimes|required|string',
            'description_ar' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric|min:0',
            'primary_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'other_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'user_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only(['title_en', 'title_ar', 'description_en', 'description_ar', 'price', 'user_id']);
        
        if ($request->has('title_en')) {
            $data['slug'] = Str::slug($request->title_en);
        }
        
        // Handle primary image upload
        if ($request->hasFile('primary_image')) {
            // Delete old image
            if ($product->primary_image) {
                Storage::disk('public')->delete($product->primary_image);
            }
            
            $primaryImage = $request->file('primary_image');
            $primaryImageName = time() . '_' . $primaryImage->getClientOriginalName();
            $primaryImage->storeAs('products', $primaryImageName, 'public');
            $data['primary_image'] = 'products/' . $primaryImageName;
        }
        
        // Handle other images upload
        if ($request->hasFile('other_images')) {
            // Delete old images
            if ($product->other_images) {
                $oldImages = json_decode($product->other_images, true);
                foreach ($oldImages as $oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
            
            $otherImages = [];
            foreach ($request->file('other_images') as $image) {
                $imageName = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();
                $image->storeAs('products', $imageName, 'public');
                $otherImages[] = 'products/' . $imageName;
            }
            $data['other_images'] = json_encode($otherImages);
        }
        
        $product->update($data);
        
        // Log activity
        ActivityLogger::log('updated_product', "Updated product: {$product->title_en}", $product);
        
        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product->load('user'),
        ], 200);
    }

    /**
     * Remove the specified product
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        
        // Delete images
        if ($product->primary_image) {
            Storage::disk('public')->delete($product->primary_image);
        }
        
        if ($product->other_images) {
            $otherImages = json_decode($product->other_images, true);
            foreach ($otherImages as $image) {
                Storage::disk('public')->delete($image);
            }
        }
        
        $productTitle = $product->title_en;
        $product->delete();
        
        // Log activity
        ActivityLogger::log('deleted_product', "Deleted product: {$productTitle}");
        
        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
        ], 200);
    }
    
    /**
     * Assign product to user
     */
    public function assignProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $product = Product::findOrFail($request->product_id);
        $user = User::findOrFail($request->user_id);
        
        $product->update(['user_id' => $user->id]);
        
        // Log activity
        ActivityLogger::log('assigned_product', "Assigned product '{$product->title_en}' to user '{$user->name}'", $product);
        
        // Send notification to user
        $user->notify(new ProductAssignedNotification($product));
        
        return response()->json([
            'success' => true,
            'message' => 'Product assigned successfully',
            'data' => $product->load('user'),
        ], 200);
    }
    
    /**
     * Unassign product from user
     */
    public function unassignProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $product = Product::findOrFail($request->product_id);
        $user = $product->user;
        $userName = $user ? $user->name : 'Unknown';
        
        $product->update(['user_id' => null]);
        
        // Log activity
        ActivityLogger::log('unassigned_product', "Unassigned product '{$product->title_en}' from user '{$userName}'", $product);
        
        // Send notification to user if exists
        if ($user) {
            $user->notify(new ProductUnassignedNotification($product));
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Product unassigned successfully',
            'data' => $product,
        ], 200);
    }
    
    /**
     * Get current user's assigned products
     */
    public function getUserProducts(Request $request)
    {
        $user = $request->user();
        $perPage = $request->get('per_page', 15);
        
        $products = Product::where('user_id', $user->id)
                          ->orderBy('created_at', 'desc')
                          ->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'data' => $products,
        ], 200);
    }
    
    /**
     * Export products to Excel/CSV
     */
    public function exportProducts(Request $request)
    {
        $format = $request->get('format', 'xlsx');
        
        // Log activity
        ActivityLogger::log('admin_exported_products', "Admin exported products data");
        
        if ($format == 'csv') {
            return Excel::download(new ProductsExport, 'products.csv', \Maatwebsite\Excel\Excel::CSV);
        } else {
            return Excel::download(new ProductsExport, 'products.xlsx');
        }
    }
}
