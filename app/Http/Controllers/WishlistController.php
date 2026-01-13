<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

class WishlistController extends Controller
{
    // Helper method to clean expired wishlist items
    private function cleanExpiredWishlistItems($userId)
    {
        Wishlist::where('user_id', $userId)
            ->where('expired_date', '<', Carbon::now())
            ->delete();
    }

    // Display wishlist page
    public function index()
    {
        $user = Auth::user();
        
        // Clean up expired items
        $this->cleanExpiredWishlistItems($user->id);
        
        $wishlistItems = Wishlist::with(['product' => function($query) {
                $query->select('product_id', 'name', 'image', 'price', 'stock', 'category_id');
            }])
            ->where('user_id', $user->id)
            ->where(function($query) {
                $query->where('expired_date', '>', Carbon::now())
                      ->orWhereNull('expired_date');
            })
            ->latest()
            ->get()
            ->map(function ($item) {
                $isExpired = $item->expired_date && Carbon::parse($item->expired_date)->isPast();
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                    'expired_date' => $item->expired_date,
                    'is_expired' => $isExpired,
                    'expires_in' => $isExpired ? null : Carbon::parse($item->expired_date)->diffForHumans(),
                    'product' => $item->product ? [
                        'product_id' => $item->product->product_id,
                        'name' => $item->product->name,
                        'image' => $item->product->image,
                        'stock' => $item->product->stock,
                        'price' => $item->product->price,
                        'category_id' => $item->product->category_id,
                    ] : null
                ];
            });

        return Inertia::render('WishlistPage', [
            'wishlistItems' => $wishlistItems,
            'itemCount' => $wishlistItems->count()
        ]);
    }

    // Add item to wishlist with 1 year expiration
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,product_id'
        ]);

        $user = Auth::user();
        $product = Product::where('product_id', $request->product_id)->first();

        if (!$product) {
            return back()->with('error', 'Product not found');
        }

        // Check if product is already in wishlist (even if expired)
        $existingWishlist = Wishlist::where('user_id', $user->id)
            ->where('product_id', $product->product_id)
            ->first();

        if ($existingWishlist) {
            // Update expiration date to 1 year from now
            $existingWishlist->update([
                'expired_date' => Carbon::now()->addYear() // Reset to 1 year from now
            ]);
            return back()->with('success', 'Wishlist item updated');
        }

        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $product->product_id,
            'expired_date' => Carbon::now()->addYear() // 1 year from now
        ]);

        return back()->with([
            'success' => 'Product added to wishlist',
            'wishlistCount' => Wishlist::where('user_id', $user->id)
                                ->where('expired_date', '>', Carbon::now())
                                ->count()
        ]);
    }

    // Add item to wishlist - OLD route for compatibility
    public function add(Request $request)
    {
        return $this->store($request);
    }

    // Toggle wishlist - OLD route (keep as JSON for frontend)
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
            
            return response()->json([
                'message' => 'Removed from wishlist',
                'action' => 'removed',
                'count' => Wishlist::where('user_id', $user->id)
                                ->where('expired_date', '>', Carbon::now())
                                ->count()
            ]);
        } else {
            // Add to wishlist with 1 year expiration
            Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $product->product_id,
                'expired_date' => Carbon::now()->addYear()
            ]);
            
            return response()->json([
                'message' => 'Added to wishlist',
                'action' => 'added',
                'count' => Wishlist::where('user_id', $user->id)
                                ->where('expired_date', '>', Carbon::now())
                                ->count()
            ]);
        }
    }

    // Remove item from wishlist
    public function destroy($id)
    {
        $user = Auth::user();
        $wishlistItem = Wishlist::where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$wishlistItem) {
            return back()->with('error', 'Item not found in wishlist');
        }

        $wishlistItem->delete();

        return back()->with([
            'success' => 'Item removed from wishlist',
            'wishlistCount' => Wishlist::where('user_id', $user->id)
                                ->where('expired_date', '>', Carbon::now())
                                ->count()
        ]);
    }

    // Remove item from wishlist - OLD route for compatibility
    public function remove($product_id)
    {
        $user = Auth::user();
        $wishlistItem = Wishlist::where('user_id', $user->id)
            ->where('product_id', $product_id)
            ->first();

        if ($wishlistItem) {
            $wishlistItem->delete();
            return back()->with('success', 'Item removed from wishlist');
        }

        return back()->with('error', 'Item not found in wishlist');
    }

    // Clear wishlist
    public function clear()
    {
        $user = Auth::user();
        
        Wishlist::where('user_id', $user->id)->delete();

        return back()->with([
            'success' => 'Wishlist cleared successfully',
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
            ->first();

        if (!$wishlistItem) {
            return back()->with('error', 'Item not found in wishlist');
        }

        // Check if product already in cart
        $existingCartItem = \App\Models\Cart::where('user_id', $user->id)
            ->where('product_id', $wishlistItem->product_id)
            ->where('status', 'active')
            ->where(function($query) {
                $query->where('expired_date', '>', Carbon::now())
                      ->orWhereNull('expired_date');
            })
            ->first();

        if ($existingCartItem) {
            return back()->with('error', 'Product already in cart');
        }

        // Add to cart with 1 month expiration
        \App\Models\Cart::create([
            'user_id' => $user->id,
            'product_id' => $wishlistItem->product_id,
            'quantity' => 1,
            'price' => $wishlistItem->product->price,
            'status' => 'active',
            'expired_date' => Carbon::now()->addMonth()
        ]);

        // Remove from wishlist
        $wishlistItem->delete();

        return back()->with('success', 'Product moved to cart successfully');
    }

    // Get wishlist count for navbar (API route - keep as JSON)
    public function getWishlistCount()
    {
        $user = Auth::user();
        $count = Wishlist::where('user_id', $user->id)
                        ->where('expired_date', '>', Carbon::now())
                        ->count();

        return response()->json(['count' => $count]);
    }
}