<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Helpers\ActivityLogger;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index()
    {
        $products = Product::with('user')->paginate(15);
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $users = User::all();
        return view('admin.products.create', compact('users'));
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title_en' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'primary_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'other_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'user_id' => 'nullable|exists:users,id',
        ]);
        
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
        
        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified product.
     */
    public function show(string $id)
    {
        $product = Product::with('user')->findOrFail($id);
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(string $id)
    {
        $product = Product::findOrFail($id);
        $users = User::all();
        return view('admin.products.edit', compact('product', 'users'));
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);
        
        $request->validate([
            'title_en' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'primary_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'other_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'user_id' => 'nullable|exists:users,id',
        ]);
        
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
        
        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product.
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
        
        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }
    
    /**
     * Export products to CSV
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        
        // Get all products with relationships
        $products = Product::with(['user'])->get();
        
        // Set headers for CSV download
        $filename = 'products_' . date('Y-m-d_His') . '.' . $format;
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Add BOM for UTF-8
        echo "\xEF\xBB\xBF";
        
        // Open output stream
        $output = fopen('php://output', 'w');
        
        // Add headers
        fputcsv($output, [
            'ID',
            'Title (EN)',
            'Title (AR)',
            'Description (EN)',
            'Description (AR)',
            'Price',
            'Assigned To',
            'User Email',
            'Created At',
            'Updated At'
        ]);
        
        // Add data rows
        foreach ($products as $product) {
            fputcsv($output, [
                $product->id,
                $product->title_en,
                $product->title_ar,
                $product->description_en,
                $product->description_ar,
                $product->price,
                $product->user ? $product->user->name : '-',
                $product->user ? $product->user->email : '-',
                $product->created_at->format('Y-m-d H:i:s'),
                $product->updated_at->format('Y-m-d H:i:s'),
            ]);
        }
        
        fclose($output);
        exit;
    }
}
