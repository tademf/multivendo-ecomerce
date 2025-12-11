<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                    'added_at' => $wishlist->created_at->format('M d, Y'),
                ];
            });

        return inertia('WishlistPage', [
            'wishlistItems' => $wishlistItems,
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

            return back()->with('success', 'Product added to wishlist!');
        }

        return back()->with('info', 'Product is already in your wishlist.');
    }

    public function destroy($productId)
    {
        $userId = Auth::id();

        Wishlist::where('user_id', $userId)
                ->where('product_id', $productId)
                ->delete();

        return back()->with('success', 'Product removed from wishlist!');
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

            return response()->json([
                'action' => 'removed',
                'message' => 'Removed from wishlist'
            ]);
        } else {
            // Add to wishlist
            Wishlist::create([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);

            return response()->json([
                'action' => 'added',
                'message' => 'Added to wishlist'
            ]);
        }
    }

    public function getWishlistIds()
    {
        $wishlistIds = Wishlist::where('user_id', Auth::id())
            ->pluck('product_id');

        return response()->json([
            'wishlist_ids' => $wishlistIds
        ]);
    }
}