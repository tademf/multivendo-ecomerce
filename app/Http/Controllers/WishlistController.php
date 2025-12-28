<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class WishlistController extends Controller
{
    // Display wishlist page
    public function index()
    {
        $user = Auth::user();
        
        $wishlistItems = Wishlist::with(['product' => function($query) {
            $query->select('product_id', 'name', 'image', 'price', 'stock');
        }])
        ->where('user_id', $user->id)
        ->latest()
        ->get();

        return Inertia::render('WishlistPage', [
            'wishlistItems' => $wishlistItems,
            'itemCount' => $wishlistItems->count()
        ]);
    }

    // Add item to wishlist - NEW route
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,product_id'
        ]);

        $user = Auth::user();
        $product = Product::where('product_id', $request->product_id)->first();

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Check if product is already in wishlist - FIXED: use product->product_id
        $existingWishlist = Wishlist::where('user_id', $user->id)
            ->where('product_id', $product->product_id) // Changed from $product->id
            ->first();

        if ($existingWishlist) {
            return response()->json(['message' => 'Product already in wishlist'], 422);
        }

        $wishlistItem = Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $product->product_id // Changed from $product->id
        ]);

        // Get updated wishlist count
        $wishlistCount = Wishlist::where('user_id', $user->id)->count();

        return response()->json([
            'message' => 'Product added to wishlist',
            'wishlistItem' => $wishlistItem->load('product'),
            'wishlistCount' => $wishlistCount
        ]);
    }

    // Add item to wishlist - OLD route for compatibility
    public function add(Request $request)
    {
        // Simply call the store method for compatibility
        return $this->store($request);
    }

    // Toggle wishlist - OLD route
    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,product_id'
        ]);

        $user = Auth::user();
        $product = Product::where('product_id', $request->product_id)->first();

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $existingWishlist = Wishlist::where('user_id', $user->id)
            ->where('product_id', $product->product_id)
            ->first();

        if ($existingWishlist) {
            // Remove from wishlist
            $existingWishlist->delete();
            $wishlistCount = Wishlist::where('user_id', $user->id)->count();
            
            return response()->json([
                'message' => 'Removed from wishlist',
                'action' => 'removed',
                'wishlistCount' => $wishlistCount
            ]);
        } else {
            // Add to wishlist
            $wishlistItem = Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $product->product_id
            ]);
            
            $wishlistCount = Wishlist::where('user_id', $user->id)->count();
            
            return response()->json([
                'message' => 'Added to wishlist',
                'action' => 'added',
                'wishlistItem' => $wishlistItem->load('product'),
                'wishlistCount' => $wishlistCount
            ]);
        }
    }

    // Remove item from wishlist - OLD route
    public function remove($product)
    {
        $user = Auth::user();
        $product = Product::where('product_id', $product)->first();

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $wishlistItem = Wishlist::where('user_id', $user->id)
            ->where('product_id', $product->product_id)
            ->first();

        if ($wishlistItem) {
            $wishlistItem->delete();
            $wishlistCount = Wishlist::where('user_id', $user->id)->count();

            return response()->json([
                'message' => 'Item removed from wishlist',
                'wishlistCount' => $wishlistCount
            ]);
        }

        return response()->json(['message' => 'Item not found in wishlist'], 404);
    }

    // Remove item from wishlist - NEW route
    public function destroy($id)
    {
        $user = Auth::user();
        $wishlistItem = Wishlist::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $wishlistItem->delete();

        // Get updated count
        $wishlistCount = Wishlist::where('user_id', $user->id)->count();

        return response()->json([
            'message' => 'Item removed from wishlist',
            'wishlistCount' => $wishlistCount
        ]);
    }

    // Clear wishlist - OLD route
    public function clear()
    {
        $user = Auth::user();
        
        Wishlist::where('user_id', $user->id)->delete();

        return response()->json([
            'message' => 'Wishlist cleared',
            'wishlistCount' => 0
        ]);
    }

    // Move item from wishlist to cart
    public function moveToCart(Request $request, $id)
    {
        $user = Auth::user();
        $wishlistItem = Wishlist::where('user_id', $user->id)
            ->where('id', $id)
            ->with('product')
            ->firstOrFail();

        // Check if product already in cart
        $existingCartItem = \App\Models\Cart::where('user_id', $user->id)
            ->where('product_id', $wishlistItem->product_id)
            ->active()
            ->first();

        if ($existingCartItem) {
            return response()->json(['message' => 'Product already in cart'], 422);
        }

        // Add to cart
        \App\Models\Cart::create([
            'user_id' => $user->id,
            'product_id' => $wishlistItem->product_id,
            'quantity' => 1,
            'price' => $wishlistItem->product->price,
            'status' => 'active'
        ]);

        // Remove from wishlist
        $wishlistItem->delete();

        // Get updated counts
        $wishlistCount = Wishlist::where('user_id', $user->id)->count();
        $cartCount = \App\Models\Cart::where('user_id', $user->id)->active()->count();

        return response()->json([
            'message' => 'Product moved to cart',
            'wishlistCount' => $wishlistCount,
            'cartCount' => $cartCount
        ]);
    }

    // Get wishlist count for navbar
    public function getWishlistCount()
    {
        $user = Auth::user();
        $count = Wishlist::where('user_id', $user->id)->count();

        return response()->json(['count' => $count]);
    }
}