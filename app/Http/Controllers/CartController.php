<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CartController extends Controller
{
    // Display cart page
    public function index()
    {
        $user = Auth::user();
        
        $cartItems = Cart::with(['product' => function($query) {
            $query->select('product_id', 'name', 'image', 'stock', 'price');
        }])
        ->where('user_id', $user->id)
        ->active()
        ->get();

        $cartTotal = $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        return Inertia::render('CartPage', [
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
            'itemCount' => $cartItems->count()
        ]);
    }

    // Add item to cart - NEW route
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'quantity' => 'nullable|integer|min:1'
        ]);

        $user = Auth::user();
        $product = Product::where('product_id', $request->product_id)->first();

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Check if product is already in cart - FIXED: use product->product_id
        $existingCartItem = Cart::where('user_id', $user->id)
            ->where('product_id', $product->product_id) // Changed from $product->id
            ->active()
            ->first();

        if ($existingCartItem) {
            // Update quantity
            $newQuantity = $existingCartItem->quantity + ($request->quantity ?? 1);
            
            if ($newQuantity > $product->stock) {
                return response()->json([
                    'message' => 'Not enough stock available',
                    'max_stock' => $product->stock
                ], 422);
            }

            $existingCartItem->update([
                'quantity' => $newQuantity
            ]);

            $message = 'Cart item updated';
            $cartItem = $existingCartItem;
        } else {
            // Add new item to cart
            if (($request->quantity ?? 1) > $product->stock) {
                return response()->json([
                    'message' => 'Not enough stock available',
                    'max_stock' => $product->stock
                ], 422);
            }

            $cartItem = Cart::create([
                'user_id' => $user->id,
                'product_id' => $product->product_id, // Changed from $product->id
                'quantity' => $request->quantity ?? 1,
                'price' => $product->price,
                'status' => 'active'
            ]);

            $message = 'Product added to cart';
        }

        // Get updated cart count
        $cartCount = Cart::where('user_id', $user->id)->active()->count();

        return response()->json([
            'message' => $message,
            'cartItem' => $cartItem->load('product'),
            'cartCount' => $cartCount
        ]);
    }

    // Add item to cart - OLD route for compatibility
    public function add(Request $request)
    {
        // Simply call the store method for compatibility
        return $this->store($request);
    }

    // Buy now method - OLD route
    public function buyNow(Request $request)
    {
        // For now, just add to cart
        return $this->store($request);
    }

    // Update cart item quantity
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $user = Auth::user();
        $cartItem = Cart::where('user_id', $user->id)
            ->where('id', $id)
            ->active()
            ->firstOrFail();

        $product = $cartItem->product;

        if ($request->quantity > $product->stock) {
            return response()->json([
                'message' => 'Not enough stock available',
                'max_stock' => $product->stock
            ], 422);
        }

        $cartItem->update([
            'quantity' => $request->quantity
        ]);

        // Get updated totals
        $cartItems = Cart::with('product')
            ->where('user_id', $user->id)
            ->active()
            ->get();

        $cartTotal = $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        return response()->json([
            'message' => 'Cart updated',
            'cartItem' => $cartItem,
            'itemTotal' => $cartItem->price * $cartItem->quantity,
            'cartTotal' => $cartTotal,
            'cartCount' => $cartItems->count()
        ]);
    }

    // Remove item from cart - OLD route
    public function remove($item)
    {
        return $this->destroy($item);
    }

    // Remove item from cart
    public function destroy($id)
    {
        $user = Auth::user();
        $cartItem = Cart::where('user_id', $user->id)
            ->where('id', $id)
            ->active()
            ->firstOrFail();

        $cartItem->update(['status' => 'removed']);

        // Get updated totals
        $cartItems = Cart::with('product')
            ->where('user_id', $user->id)
            ->active()
            ->get();

        $cartTotal = $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        return response()->json([
            'message' => 'Item removed from cart',
            'cartTotal' => $cartTotal,
            'cartCount' => $cartItems->count()
        ]);
    }

    // Clear entire cart
    public function clear()
    {
        $user = Auth::user();
        
        Cart::where('user_id', $user->id)
            ->active()
            ->update(['status' => 'removed']);

        return response()->json([
            'message' => 'Cart cleared',
            'cartCount' => 0,
            'cartTotal' => 0
        ]);
    }

    // Get cart count for navbar
    public function getCartCount()
    {
        $user = Auth::user();
        $count = Cart::where('user_id', $user->id)->active()->count();

        return response()->json(['count' => $count]);
    }
}