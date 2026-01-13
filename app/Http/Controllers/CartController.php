<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CartController extends Controller
{
    // Helper method to clean expired items
    private function cleanExpiredCartItems($userId)
    {
        Cart::where('user_id', $userId)
            ->where('status', 'active')
            ->where('expired_date', '<', Carbon::now())
            ->delete();
    }

    // Display cart page
    public function index()
    {
        $user = Auth::user();
        
        // Clean up expired items
        $this->cleanExpiredCartItems($user->id);
        
        // Clean up any items with status 'removed' (PERMANENT DELETE)
        Cart::where('user_id', $user->id)
            ->where('status', 'removed')
            ->delete();
        
        // Now fetch only active AND non-expired items
        $cartItems = Cart::with(['product' => function($query) {
                $query->select('product_id', 'name', 'image', 'stock', 'price', 'category_id');
            }])
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->where(function($query) {
                $query->where('expired_date', '>', Carbon::now())
                      ->orWhereNull('expired_date');
            })
            ->get()
            ->map(function ($item) {
                $isExpired = $item->expired_date && Carbon::parse($item->expired_date)->isPast();
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
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

        // Calculate total for non-expired items only
        $activeCartItems = $cartItems->where('is_expired', false);
        $cartTotal = $activeCartItems->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        return Inertia::render('CartPage', [
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
            'itemCount' => $activeCartItems->count()
        ]);
    }

    // Add item to cart with 1 month expiration
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'quantity' => 'nullable|integer|min:1'
        ]);

        $user = Auth::user();
        $product = Product::where('product_id', $request->product_id)->first();

        if (!$product) {
            return back()->with('error', 'Product not found');
        }

        // Use DB transaction to handle unique constraint
        DB::beginTransaction();
        
        try {
            // First, check if there's a removed item for this product
            $existingRemovedItem = Cart::where('user_id', $user->id)
                ->where('product_id', $product->product_id)
                ->where('status', 'removed')
                ->first();
            
            if ($existingRemovedItem) {
                // DELETE the removed item first to avoid unique constraint violation
                $existingRemovedItem->delete();
            }
            
            // Now check for active item
            $existingCartItem = Cart::where('user_id', $user->id)
                ->where('product_id', $product->product_id)
                ->where('status', 'active')
                ->first();

            if ($existingCartItem) {
                // Update quantity of existing active item
                $newQuantity = $existingCartItem->quantity + ($request->quantity ?? 1);
                
                if ($newQuantity > $product->stock) {
                    DB::rollBack();
                    return back()->with('error', 'Not enough stock available. Max stock: ' . $product->stock);
                }

                $existingCartItem->update([
                    'quantity' => $newQuantity,
                    'expired_date' => Carbon::now()->addMonth() // Reset to 1 month from now
                ]);

                $message = 'Cart item updated';
            } else {
                // Add new item to cart
                if (($request->quantity ?? 1) > $product->stock) {
                    DB::rollBack();
                    return back()->with('error', 'Not enough stock available. Max stock: ' . $product->stock);
                }

                Cart::create([
                    'user_id' => $user->id,
                    'product_id' => $product->product_id,
                    'quantity' => $request->quantity ?? 1,
                    'price' => $product->price,
                    'status' => 'active',
                    'expired_date' => Carbon::now()->addMonth() // 1 month from now
                ]);

                $message = 'Product added to cart';
            }
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to add item to cart: ' . $e->getMessage());
        }

        return redirect()->route('cart.index')->with([
            'success' => $message,
            'cartCount' => Cart::where('user_id', $user->id)
                            ->where('status', 'active')
                            ->where('expired_date', '>', Carbon::now())
                            ->count()
        ]);
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
            ->where('status', 'active')
            ->first();

        if (!$cartItem) {
            return back()->with('error', 'Cart item not found');
        }

        $product = Product::where('product_id', $cartItem->product_id)->first();

        if (!$product) {
            return back()->with('error', 'Product not found');
        }

        if ($request->quantity > $product->stock) {
            return back()->with('error', 'Not enough stock available. Max stock: ' . $product->stock);
        }

        // Update quantity and reset expiration date
        $cartItem->update([
            'quantity' => $request->quantity,
            'expired_date' => Carbon::now()->addMonth() // Reset to 1 month from now
        ]);

        return back()->with('success', 'Quantity updated');
    }

    // Remove item from cart
    public function destroy($id)
    {
        $user = Auth::user();
        $cartItem = Cart::where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$cartItem) {
            return back()->with('error', 'Cart item not found');
        }

        $cartItem->delete();

        return back()->with([
            'success' => 'Item removed from cart',
            'cartCount' => Cart::where('user_id', $user->id)
                            ->where('status', 'active')
                            ->where('expired_date', '>', Carbon::now())
                            ->count()
        ]);
    }

    // Clear entire cart
    public function clear()
    {
        $user = Auth::user();
        
        Cart::where('user_id', $user->id)->delete();

        return back()->with([
            'success' => 'Cart cleared successfully',
            'cartCount' => 0
        ]);
    }

    // Get cart count for navbar
    public function getCartCount()
    {
        $user = Auth::user();
        $count = Cart::where('user_id', $user->id)
                    ->where('status', 'active')
                    ->where('expired_date', '>', Carbon::now())
                    ->count();

        return response()->json(['count' => $count]);
    }

    // Get cart total for navbar
    public function getCartTotal()
    {
        $user = Auth::user();
        $total = Cart::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('expired_date', '>', Carbon::now())
            ->with('product')
            ->get()
            ->sum(function ($item) {
                return $item->price * $item->quantity;
            });

        return response()->json(['total' => $total]);
    }
}