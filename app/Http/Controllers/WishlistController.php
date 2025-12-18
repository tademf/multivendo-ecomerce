<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class WishlistController extends Controller
{
    public function index()
    {
        // Get wishlist items directly from Wishlist model
        $wishlistItems = Wishlist::where('user_id', Auth::id())
            ->with('product.category')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($wishlist) {
                $product = $wishlist->product;
                return [
                    'product_id' => $product->product_id,
                    'name' => $product->name,
                    'price' => floatval($product->price),
                    'image' => $product->image ? asset('storage/' . $product->image) : 'https://placehold.co/400x300/e0f2f1/065f46?text=Product+Image',
                    'category' => $product->category->name ?? 'Uncategorized',
                    'stock' => $product->stock ?? 0,
                    'description' => $product->description ?? '',
                    'reference' => $product->reference ?? '',
                    'created_at' => $wishlist->created_at,
                    'added_at' => $wishlist->created_at->format('M d, Y'),
                ];
            });

        // Get suggested products (excluding wishlist items)
        $wishlistProductIds = $wishlistItems->pluck('product_id')->toArray();
        $suggestedProducts = Product::whereNotIn('product_id', $wishlistProductIds)
            ->where('stock', '>', 0)
            ->inRandomOrder()
            ->limit(4)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->product_id,
                    'name' => $product->name,
                    'price' => floatval($product->price),
                    'image' => $product->image ? asset('storage/' . $product->image) : 'https://placehold.co/300x200/e0f2f1/065f46?text=Product',
                    'stock' => $product->stock,
                ];
            });

        return Inertia::render('WishlistPage', [
            'wishlistItems' => $wishlistItems,
            'suggestedProducts' => $suggestedProducts,
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
                'info' => session('info'),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,product_id',
        ]);

        $userId = Auth::id();
        $productId = $request->product_id;

        // Check if already in wishlist
        $exists = Wishlist::where('user_id', $userId)
                         ->where('product_id', $productId)
                         ->exists();

        if (!$exists) {
            Wishlist::create([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);

            // Get product name for success message
            $product = Product::find($productId);
            
            return redirect()->back()->with('success', "{$product->name} added to wishlist successfully!");
        }

        return redirect()->back()->with('info', 'Product is already in your wishlist.');
    }

    public function destroy($productId)
    {
        $userId = Auth::id();

        // Get product name before deleting for message
        $product = Product::find($productId);
        
        Wishlist::where('user_id', $userId)
                ->where('product_id', $productId)
                ->delete();

        return redirect()->back()->with('success', "{$product->name} removed from wishlist!");
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,product_id',
        ]);

        $userId = Auth::id();
        $productId = $request->product_id;

        $exists = Wishlist::where('user_id', $userId)
                         ->where('product_id', $productId)
                         ->exists();

        if ($exists) {
            // Remove from wishlist
            Wishlist::where('user_id', $userId)
                    ->where('product_id', $productId)
                    ->delete();

            // Get product name for message
            $product = Product::find($productId);
            
            return redirect()->back()->with('success', "{$product->name} removed from wishlist!");
        } else {
            // Add to wishlist
            Wishlist::create([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);

            // Get product name for message
            $product = Product::find($productId);
            
            return redirect()->back()->with('success', "{$product->name} added to wishlist successfully!");
        }
    }

    public function clear()
    {
        $userId = Auth::id();
        
        $count = Wishlist::where('user_id', $userId)->count();
        
        if ($count > 0) {
            Wishlist::where('user_id', $userId)->delete();
            return redirect()->back()->with('success', "All {$count} items removed from wishlist!");
        }
        
        return redirect()->back()->with('info', 'Your wishlist is already empty.');
    }

    public function getWishlistIds(Request $request)
    {
        // This is for AJAX calls - still returns JSON but only for internal use
        $wishlistIds = Wishlist::where('user_id', Auth::id())
            ->pluck('product_id')
            ->toArray();

        // Return as array for Inertia props
        return $wishlistIds;
    }

    public function addFromSuggestion(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,product_id',
        ]);

        $userId = Auth::id();
        $productId = $request->product_id;

        // Check if already in wishlist
        $exists = Wishlist::where('user_id', $userId)
                         ->where('product_id', $productId)
                         ->exists();

        if (!$exists) {
            Wishlist::create([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);

            // Get product name for success message
            $product = Product::find($productId);
            
            return redirect()->route('wishlist.index')->with('success', "{$product->name} added to wishlist!");
        }

        return redirect()->route('wishlist.index')->with('info', 'Product is already in your wishlist.');
    }
    
    
}
