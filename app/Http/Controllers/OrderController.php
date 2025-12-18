<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * Display customer orders page
     */
    public function index()
    {
        return $this->customerOrders();
    }

    /**
     * Display customer orders page
     */
    public function customerOrders()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to view your orders.');
        }
        
        // Get orders for this customer - FIXED: Get from orders table directly
        $orders = Order::where('user_id', $user->id)
            ->with('payment') // Load payment relationship if exists
            ->latest()
            ->get();
        
        // Format orders for display
        $orders->transform(function ($order) {
            return $this->formatOrderForDisplay($order, false);
        });
        
        return Inertia::render('Orders/CustomerOrders', [
            'orders' => $orders,
            'user' => $user,
        ]);
    }

    /**
     * Display vendor orders page (for verified users)
     */
    public function vendorOrders()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to view orders.');
        }
        
        // Check if user is verified
        $isVerified = !empty($user->verified_at);
        
        // Get vendor's products
        $vendorProducts = Product::where('user_id', $user->id)->pluck('product_id')->toArray();
        
        $orders = collect([]);
        
        if (!empty($vendorProducts)) {
            // Get orders that contain vendor's product IDs
            $orders = Order::whereIn('product_id', $vendorProducts)
                ->with(['payment', 'customer']) // Load relationships
                ->latest()
                ->get();
        }
        
        // Format orders for display
        $orders->transform(function ($order) {
            return $this->formatOrderForDisplay($order, true);
        });
        
        return Inertia::render('Orders/VendorOrders', [
            'orders' => $orders,
            'user' => $user,
            'isVerifiedVendor' => $isVerified,
            'hasProducts' => !empty($vendorProducts),
        ]);
    }

    /**
     * Helper method to format order for display
     */
    private function formatOrderForDisplay($order, $isVendorView = false)
    {
        // Extract product info from cancellation_reason field
        $productInfo = $this->extractProductInfo($order->cancellation_reason);
        
        // Add extracted info to order object
        $order->product_id = $productInfo['product_id'] ?? $order->product_id;
        $order->product_name = $productInfo['product_name'] ?? $order->product_name;
        $order->quantity = $productInfo['quantity'] ?? $order->quantity;
        $order->vendor_id = $productInfo['vendor_id'] ?? null;
        $order->vendor_account_number = $productInfo['vendor_account'] ?? null;
        
        // Format currency with "Birr"
        $order->formatted_amount = number_format($order->amount, 0) . ' Birr';
        $order->unit_price_formatted = number_format($order->amount / ($order->quantity ?? 1), 0) . ' Birr';
        
        // Get payment info if payment relationship exists
        if ($order->payment) {
            $order->payment_status = $order->payment->status ?? 'pending';
            $order->payment_image = $order->payment->payment_image ?? null;
            $order->payment_reference = $order->payment->order_reference ?? null;
        } else {
            $order->payment_status = 'pending';
            $order->payment_image = $order->payment_image ?? null;
            $order->payment_reference = null;
        }
        
        // Add customer/vendor info
        if ($isVendorView) {
            // Try to get customer from user relationship
            if ($order->customer) {
                $order->customer_name = $order->customer->name ?? $order->customer->full_name ?? 'Customer';
                $order->customer_email = $order->customer->email ?? null;
            } else {
                // Fallback to order name field
                $order->customer_name = $order->name ?? 'Customer';
                $order->customer_email = null;
            }
        }
        
        if (!$isVendorView && $order->vendor_id) {
            $vendor = User::find($order->vendor_id);
            if ($vendor) {
                $order->vendor_name = $vendor->name ?? $vendor->full_name ?? 'Vendor';
            }
        }
        
        return $order;
    }

    /**
     * Extract product info from cancellation_reason field
     */
    private function extractProductInfo($cancellationReason)
    {
        $info = [];
        
        if (!$cancellationReason) {
            return $info;
        }
        
        // Parse the notes format
        $parts = explode(' | ', $cancellationReason);
        
        foreach ($parts as $part) {
            if (strpos($part, ': ') !== false) {
                list($key, $value) = explode(': ', $part, 2);
                $key = strtolower(str_replace(' ', '_', trim($key)));
                $info[$key] = trim($value);
            }
        }
        
        return $info;
    }

    /**
     * Cancel an order
     */
    public function cancel(Request $request, Order $order)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|min:10|max:500',
        ]);

        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Authentication required'], 401);
        }
        
        // Check permissions
        $isCustomer = $order->user_id === $user->id;
        $isVendor = false;
        
        if (!$isCustomer) {
            // Check if user is vendor for this order
            $productInfo = $this->extractProductInfo($order->cancellation_reason);
            $isVendor = isset($productInfo['vendor_id']) && $productInfo['vendor_id'] == $user->id;
            
            if (!$isVendor) {
                return response()->json(['error' => 'Unauthorized action.'], 403);
            }
        }

        // Check if order can be cancelled
        if (!in_array($order->status, ['pending', 'processing'])) {
            return response()->json(['error' => 'Order cannot be cancelled at this stage.'], 400);
        }

        try {
            DB::beginTransaction();

            // Extract product info to restore stock
            $productInfo = $this->extractProductInfo($order->cancellation_reason);
            
            if (isset($productInfo['product_id']) && isset($productInfo['quantity'])) {
                $product = Product::find($productInfo['product_id']);
                if ($product) {
                    $product->increment('stock', $productInfo['quantity']);
                    Log::info('Stock restored', ['product_id' => $productInfo['product_id'], 'quantity' => $productInfo['quantity']]);
                }
            }

            // Update order
            $order->update([
                'status' => 'cancelled',
                'cancellation_reason' => $request->cancellation_reason . ' | Cancelled at: ' . now(),
            ]);

            // Update payment if exists
            if ($order->payment_id) {
                $payment = Payment::find($order->payment_id);
                if ($payment) {
                    $payment->update(['status' => 'cancelled']);
                }
            }

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully.',
                'order' => $this->formatOrderForDisplay($order->fresh(), $isVendor)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order cancellation failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Error cancelling order: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update order status (for vendors)
     */
    public function updateStatus(Request $request, Order $order)
    {
        $user = Auth::user();
        
        // Check if user is verified vendor
        if (!$user->verified_at) {
            return response()->json(['error' => 'Only verified vendors can update order status.'], 403);
        }
        
        // Check if user is vendor for this order
        $productInfo = $this->extractProductInfo($order->cancellation_reason);
        if (!isset($productInfo['vendor_id']) || $productInfo['vendor_id'] != $user->id) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered'
        ]);

        // Don't allow changing to cancelled through this method
        if ($request->status === 'cancelled') {
            return response()->json(['error' => 'Please use the cancel order method to cancel orders.'], 400);
        }

        $order->update(['status' => $request->status]);
        
        // Update payment when delivered
        if ($request->status === 'delivered' && $order->payment_id) {
            $payment = Payment::find($order->payment_id);
            if ($payment) {
                $payment->update(['status' => 'completed']);
            }
        }
        
        Log::info('Order status updated', [
            'order_id' => $order->id,
            'new_status' => $request->status,
            'user_id' => $user->id
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Order status updated to ' . ucfirst($request->status) . ' successfully.',
            'order' => $this->formatOrderForDisplay($order->fresh(), true)
        ]);
    }

    /**
     * Show order details
     */
    public function show(Order $order)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Check permissions
        $isCustomer = $order->user_id === $user->id;
        $isVendor = false;
        
        if (!$isCustomer) {
            // Check if user is vendor for this order
            $productInfo = $this->extractProductInfo($order->cancellation_reason);
            $isVendor = isset($productInfo['vendor_id']) && $productInfo['vendor_id'] == $user->id;
            
            if (!$isVendor) {
                abort(403, 'Unauthorized action.');
            }
        }

        // Format order for display
        $formattedOrder = $this->formatOrderForDisplay($order, $isVendor);

        return Inertia::render('Orders/OrderDetail', [
            'order' => $formattedOrder,
            'isVendor' => $isVendor
        ]);
    }

    /**
     * Debug method to check if orders are being created properly
     */
    public function debugOrders()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $user = Auth::user();
        
        $orders = Order::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
            
        $payments = Payment::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
            
        return response()->json([
            'user_id' => $user->id,
            'orders_count' => $orders->count(),
            'payments_count' => $payments->count(),
            'orders' => $orders,
            'payments' => $payments,
            'orders_table_schema' => [
                'id',
                'user_id',
                'product_id',
                'product_name',
                'product_image',
                'payment_id',
                'name',
                'amount',
                'quantity',
                'shipment_address',
                'payment_image',
                'status',
                'cancellation_reason',
                'order_number',
                'created_at',
                'updated_at'
            ]
        ]);
    }
}