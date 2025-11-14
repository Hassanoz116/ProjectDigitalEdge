<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    /**
     * Display a listing of gallery images.
     */
    public function index()
    {
        $products = Product::whereNotNull('primary_image')->get();
        $images = [];
        
        foreach ($products as $product) {
            // Add primary image
            $images[] = [
                'id' => $product->id . '_primary',
                'product_id' => $product->id,
                'product_title' => app()->getLocale() == 'ar' ? $product->title_ar : $product->title_en,
                'path' => $product->primary_image,
                'type' => 'primary',
            ];
            
            // Add other images
            if ($product->other_images) {
                $otherImages = json_decode($product->other_images, true);
                foreach ($otherImages as $index => $imagePath) {
                    $images[] = [
                        'id' => $product->id . '_other_' . $index,
                        'product_id' => $product->id,
                        'product_title' => app()->getLocale() == 'ar' ? $product->title_ar : $product->title_en,
                        'path' => $imagePath,
                        'type' => 'other',
                        'index' => $index,
                    ];
                }
            }
        }
        
        return view('admin.gallery.index', compact('images'));
    }

    /**
     * Upload a new image.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'product_id' => 'required|exists:products,id',
        ]);
        
        $product = Product::findOrFail($request->product_id);
        
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();
            $image->storeAs('products', $imageName, 'public');
            $imagePath = 'products/' . $imageName;
            
            // Add to other images
            $otherImages = $product->other_images ? json_decode($product->other_images, true) : [];
            $otherImages[] = $imagePath;
            $product->update(['other_images' => json_encode($otherImages)]);
        }
        
        return redirect()->route('admin.gallery.index')
            ->with('success', 'Image uploaded successfully.');
    }

    /**
     * Remove the specified image.
     */
    public function destroy(string $id)
    {
        // Parse the ID to get product ID and image type
        $parts = explode('_', $id);
        $productId = $parts[0];
        $type = $parts[1];
        
        $product = Product::findOrFail($productId);
        
        if ($type === 'primary') {
            // Delete primary image
            if ($product->primary_image) {
                Storage::disk('public')->delete($product->primary_image);
                $product->update(['primary_image' => null]);
            }
        } else if ($type === 'other') {
            // Delete other image
            $index = $parts[2];
            $otherImages = json_decode($product->other_images, true);
            
            if (isset($otherImages[$index])) {
                Storage::disk('public')->delete($otherImages[$index]);
                unset($otherImages[$index]);
                $otherImages = array_values($otherImages); // Re-index array
                $product->update(['other_images' => json_encode($otherImages)]);
            }
        }
        
        return redirect()->route('admin.gallery.index')
            ->with('success', 'Image deleted successfully.');
    }
}
