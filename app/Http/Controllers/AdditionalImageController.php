<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\AdditionalImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AdditionalImageController extends Controller
{
    use AuthorizesRequests;

    public function index($productId)
    {
        Log::info('AdditionalImageController index called for product: ' . $productId);
        
        $product = Product::findOrFail($productId);
        
        // Check if user owns the product
        $this->authorize('view', $product);
        
        $images = $product->additionalImages()->ordered()->get();
        
        return response()->json([
            'success' => true,
            'images' => $images
        ]);
    }

    public function store(Request $request, $productId)
    {
        Log::info('AdditionalImageController store called for product: ' . $productId);
        Log::info('Files count: ' . count($request->file('images', [])));
        
        try {
            $product = Product::findOrFail($productId);
            
            // Check if user owns the product
            $this->authorize('update', $product);
            
            $request->validate([
                'images' => 'required|array|min:1',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);
            
            $uploadedImages = [];
            
            foreach ($request->file('images') as $image) {
                $path = $image->store('products/additional', 'public');
                
                $maxOrder = AdditionalImage::where('product_id', $product->product_id)->max('display_order');
                
                $additionalImage = AdditionalImage::create([
                    'product_id' => $product->product_id,
                    'image_path' => $path,
                    'display_order' => ($maxOrder ? $maxOrder + 1 : 1)
                ]);
                
                $uploadedImages[] = $additionalImage;
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Images uploaded successfully',
                'images' => $uploadedImages
            ], 201);

        } catch (\Exception $e) {
            Log::error('Image upload error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error uploading images: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        Log::info('AdditionalImageController destroy called for image: ' . $id);
        
        try {
            $image = AdditionalImage::findOrFail($id);
            $product = $image->product;
            
            // Check if user owns the product
            $this->authorize('update', $product);
            
            // Delete file from storage
            if ($image->image_path && Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
            
            $image->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Image delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting image: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateOrder(Request $request, $productId)
    {
        Log::info('AdditionalImageController updateOrder called for product: ' . $productId);
        
        try {
            $product = Product::findOrFail($productId);
            
            // Check if user owns the product
            $this->authorize('update', $product);
            
            $request->validate([
                'order' => 'required|array'
            ]);
            
            foreach ($request->order as $index => $imageId) {
                AdditionalImage::where('additional_image_id', $imageId)
                              ->where('product_id', $product->product_id)
                              ->update(['display_order' => $index + 1]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Update order error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function setSelected(Request $request, $id)
    {
        Log::info('AdditionalImageController setSelected called for image: ' . $id);
        
        try {
            $image = AdditionalImage::findOrFail($id);
            $product = $image->product;
            
            // Check if user owns the product
            $this->authorize('update', $product);
            
            // First, unselect all images for this product
            AdditionalImage::where('product_id', $product->product_id)
                          ->update(['is_selected' => false]);
            
            // Select the current image
            $image->update(['is_selected' => true]);
            
            return response()->json([
                'success' => true,
                'message' => 'Image selected successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Set selected error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error selecting image: ' . $e->getMessage()
            ], 500);
        }
    }
}