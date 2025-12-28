<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Tag;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProductController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $products = Product::with(['category', 'tags'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        $categories = Category::all();
        $allTags = Tag::all();

        return Inertia::render('ProductsPage', [
            'products' => $products,
            'categories' => $categories,
            'allTags' => $allTags, // Add this line
            'success' => session('success'),
            'error' => session('error')
        ]);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Basic validation
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'reference' => 'nullable|string|unique:products,reference',
                'description' => 'nullable|string',
                'product_type' => 'required|in:one-time,onstock',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'category_id' => 'required|exists:categories,category_id',
                'selectedTags' => 'nullable|array',
                'selectedTags.*' => 'exists:tags,tag_id',
                'price_tiers' => 'nullable|array',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'manage_stock' => 'nullable|boolean',
                'minimum_stock' => 'nullable|integer|min:0',
                'low_stock_threshold' => 'nullable|integer|min:0',
                'status' => 'nullable|string|in:active,inactive,draft'
            ]);

            // Handle price based on product type
            $finalPrice = $request->price;
            $priceTiers = null;
            
            if ($request->product_type === 'onstock' && $request->has('price_tiers')) {
                $priceTiers = $request->price_tiers;
                // Set the first tier price as the base price
                if (!empty($priceTiers) && isset($priceTiers[0]['price'])) {
                    $finalPrice = $priceTiers[0]['price'];
                }
            }

            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products', 'public');
            }

            // Create product
            $product = Product::create([
                'name' => $validated['name'],
                'reference' => $validated['reference'] ?? null,
                'description' => $validated['description'] ?? null,
                'product_type' => $validated['product_type'],
                'price' => $finalPrice,
                'price_tiers' => $priceTiers ? json_encode($priceTiers) : null,
                'stock' => $validated['stock'],
                'category_id' => $validated['category_id'],
                'user_id' => Auth::id(),
                'image' => $imagePath,
                'manage_stock' => $validated['manage_stock'] ?? true,
                'minimum_stock' => $validated['minimum_stock'] ?? 10,
                'low_stock_threshold' => $validated['low_stock_threshold'] ?? 5,
                'status' => $validated['status'] ?? 'active'
            ]);

            // Sync tags if provided
            if ($request->has('selectedTags') && is_array($request->selectedTags)) {
                $product->tags()->sync($request->selectedTags);
            }

            DB::commit();

            return redirect()->route('products.index')
                ->with('success', 'Product created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error creating product: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Product $product)
    {
        try {
            // Authorize using the ProductPolicy
            $this->authorize('update', $product);

            DB::beginTransaction();

            // Basic validation
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'reference' => 'required|string|unique:products,reference,' . $product->product_id . ',product_id',
                'description' => 'nullable|string',
                'product_type' => 'required|in:one-time,onstock',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'category_id' => 'required|exists:categories,category_id',
                'selectedTags' => 'nullable|array',
                'selectedTags.*' => 'exists:tags,tag_id',
                'price_tiers' => 'nullable|array',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'manage_stock' => 'nullable|boolean',
                'minimum_stock' => 'nullable|integer|min:0',
                'low_stock_threshold' => 'nullable|integer|min:0',
                'status' => 'nullable|string|in:active,inactive,draft'
            ]);

            // Handle price based on product type
            $finalPrice = $request->price;
            $priceTiers = null;
            
            if ($request->product_type === 'onstock' && $request->has('price_tiers')) {
                $priceTiers = $request->price_tiers;
                // Set the first tier price as the base price
                if (!empty($priceTiers) && isset($priceTiers[0]['price'])) {
                    $finalPrice = $priceTiers[0]['price'];
                }
            } else {
                // Clear price tiers for one-time products
                $priceTiers = null;
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $imagePath = $request->file('image')->store('products', 'public');
                $product->image = $imagePath;
            }

            // Update product
            $product->update([
                'name' => $validated['name'],
                'reference' => $validated['reference'],
                'description' => $validated['description'] ?? null,
                'product_type' => $validated['product_type'],
                'price' => $finalPrice,
                'price_tiers' => $priceTiers ? json_encode($priceTiers) : null,
                'stock' => $validated['stock'],
                'category_id' => $validated['category_id'],
                'manage_stock' => $validated['manage_stock'] ?? true,
                'minimum_stock' => $validated['minimum_stock'] ?? 10,
                'low_stock_threshold' => $validated['low_stock_threshold'] ?? 5,
                'status' => $validated['status'] ?? 'active'
            ]);

            // Sync tags
            if ($request->has('selectedTags') && is_array($request->selectedTags)) {
                $product->tags()->sync($request->selectedTags);
            } else {
                // If no tags provided, detach all
                $product->tags()->detach();
            }

            DB::commit();

            return redirect()->route('products.index')
                ->with('success', 'Product updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error updating product: ' . $e->getMessage());
        }
    }

    public function destroy(Product $product)
    {
        try {
            // Authorize using the ProductPolicy
            $this->authorize('delete', $product);

            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $product->delete();

            return redirect()->route('products.index')
                ->with('success', 'Product deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting product: ' . $e->getMessage());
        }
    }

    // NEW METHOD: Show page for adding additional images
    public function showAddImages($id)
    {
        $product = Product::with(['additionalImages', 'category'])
                         ->findOrFail($id);
        
        // Check if user owns the product
        if (Auth::id() !== $product->user_id) {
            abort(403, 'Unauthorized action.');
        }
        
        return Inertia::render('AddImagesPage', [
            'product' => $product,
            'success' => session('success'),
            'error' => session('error')
        ]);
    }

    // NEW METHOD: Show product details with all images for public
    public function showDetails($id)
    {
        $product = Product::with(['category', 'additionalImages', 'user', 'discount'])
                         ->findOrFail($id);
        
        $isOwner = Auth::check() && Auth::id() === $product->user_id;
        
        return Inertia::render('ProductDetails', [
            'product' => $product,
            'isOwner' => $isOwner,
            'allImages' => $product->all_images,
            'selectedImageId' => request()->query('selected_image_id')
        ]);
    }

    // NEW METHOD: Show public product details
    public function show(Product $product)
    {
        $product->load(['category', 'additionalImages', 'user', 'discount']);
        
        $isOwner = Auth::check() && Auth::id() === $product->user_id;
        
        return Inertia::render('ProductDetails', [
            'product' => $product,
            'isOwner' => $isOwner,
            'allImages' => $product->all_images,
            'selectedImageId' => request()->query('selected_image_id')
        ]);
    }
}