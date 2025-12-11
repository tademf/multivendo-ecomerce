<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * Display a listing of the user's orders.
     */
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->latest()
            ->get();
        
        return Inertia::render('OrdersPage', [
            'orders' => $orders,
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ]
        ]);
    }

    /**
     * Cancel an order with a reason.
     */
    public function cancel(Request $request, Order $order)
    {
        // Validate cancellation reason
        $request->validate([
            'cancellation_reason' => 'required|string|min:10|max:500',
            'product_id' => 'nullable|exists:products,product_id', // Add product_id for quantity restore
            'quantity' => 'nullable|integer|min:1', // Add quantity for restore
        ]);

        // Check if user owns this order
        if ($order->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        // Check if order can be cancelled
        if (!in_array($order->status, ['pending', 'processing'])) {
            return response()->json(['error' => 'Order cannot be cancelled at this stage.'], 400);
        }

        try {
            DB::beginTransaction();

            // Restore product quantity if product_id is provided
            if ($request->has('product_id') && $request->has('quantity')) {
                $product = Product::find($request->product_id);
                if ($product) {
                    $product->increment('stock', $request->quantity);
                }
            }

            // Update order
            $order->update([
                'status' => 'cancelled',
                'cancellation_reason' => $request->cancellation_reason,
            ]);

            DB::commit();

            return response()->json(['success' => 'Order cancelled successfully.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error cancelling order: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update order status (for Processing, Shipped, Delivered).
     */
    public function updateStatus(Request $request, Order $order)
    {
        // Check if user owns this order
        if ($order->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        // If changing to cancelled, redirect to cancel method
        if ($request->status === 'cancelled') {
            return redirect()->back()->with('error', 'Please use the cancel order form to provide a cancellation reason.');
        }

        // Update order status
        $order->update([
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Order status updated to ' . ucfirst($request->status) . ' successfully.');
    }

    /**
     * Show order details (optional - if you want a separate detail page).
     */
    public function show(Order $order)
    {
        // Check if user owns this order
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return Inertia::render('OrderDetailPage', [
            'order' => $order
        ]);
    }
}