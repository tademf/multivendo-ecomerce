<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\Product;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class ShipmentController extends Controller
{
    /**
     * Display customer orders page
     */
    public function customerOrders()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to view your orders.');
        }
        
        // Get customer's shipments/orders
        $shipments = Shipment::where('user_id', $user->id)
            ->with(['payment', 'vendor'])
            ->latest()
            ->get();
        
        // Format shipments for customer view
        $shipments->transform(function ($shipment) {
            return $this->formatShipmentForCustomerView($shipment);
        });
        
        return Inertia::render('Orders/CustomerOrders', [
            'orders' => $shipments,
            'user' => $user,
        ]);
    }
    
    /**
     * Display vendor orders page
     */
    public function vendorOrders()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to view orders.');
        }
        
        // Get vendor's products
        $vendorProducts = Product::where('user_id', $user->id)->pluck('product_id')->toArray();
        
        $shipments = collect([]);
        
        if (!empty($vendorProducts)) {
            // Get shipments that contain vendor's product IDs
            $shipments = Shipment::whereIn('product_id', $vendorProducts)
                ->with(['payment', 'customer'])
                ->latest()
                ->get();
        }
        
        // Format shipments for vendor view
        $shipments->transform(function ($shipment) {
            return $this->formatShipmentForVendorView($shipment);
        });
        
        return Inertia::render('Orders/VendorOrders', [
            'orders' => $shipments,
            'user' => $user,
        ]);
    }
    
    /**
     * Format shipment for customer view
     */
    private function formatShipmentForCustomerView($shipment)
    {
        // Amount is already the actual paid price (discounted or original)
        $shipment->formatted_amount = number_format($shipment->amount, 2) . ' Birr';
        
        // Get vendor info from product owner
        $product = Product::where('product_id', $shipment->product_id)->first();
        if ($product && $product->user) {
            $vendor = $product->user;
            $shipment->vendor_name = $vendor->name ?? $vendor->full_name ?? 'Vendor';
            $shipment->vendor_email = $vendor->email ?? null;
        }
        
        // Get payment status
        if ($shipment->payment) {
            $shipment->payment_status = $shipment->payment->status ?? 'pending';
            $shipment->payment_image = $shipment->payment->payment_image ?? null;
            $shipment->payment_reference = $shipment->payment->order_reference ?? null;
        } else {
            $shipment->payment_status = 'pending';
        }
        
        // Format discount info if applicable
        if ($shipment->is_discounted && $shipment->original_price) {
            $shipment->original_price_formatted = number_format($shipment->original_price, 2) . ' Birr';
            $savings = $shipment->original_price - $shipment->amount;
            $shipment->savings_formatted = number_format($savings, 2) . ' Birr';
            
            // Calculate discount percentage
            if ($shipment->original_price > 0) {
                $shipment->discount_percentage = round(($savings / $shipment->original_price) * 100);
            }
        }
        
        return $shipment;
    }
    
    /**
     * Format shipment for vendor view
     */
    private function formatShipmentForVendorView($shipment)
    {
        // Amount is already the actual received price (discounted or original)
        $shipment->formatted_amount = number_format($shipment->amount, 2) . ' Birr';
        
        // Get customer info
        if ($shipment->customer) {
            $shipment->customer_name = $shipment->customer->name ?? $shipment->customer->full_name ?? 'Customer';
            $shipment->customer_email = $shipment->customer->email ?? null;
        } else {
            $shipment->customer_name = $shipment->name ?? 'Customer';
            $shipment->customer_email = null;
        }
        
        // Get payment status
        if ($shipment->payment) {
            $shipment->payment_status = $shipment->payment->status ?? 'pending';
            $shipment->payment_image = $shipment->payment->payment_image ?? null;
            $shipment->payment_reference = $shipment->payment->order_reference ?? null;
        } else {
            $shipment->payment_status = 'pending';
        }
        
        // Format discount info if applicable
        if ($shipment->is_discounted && $shipment->original_price) {
            $shipment->original_price_formatted = number_format($shipment->original_price, 2) . ' Birr';
            $savings = $shipment->original_price - $shipment->amount;
            $shipment->savings_formatted = number_format($savings, 2) . ' Birr';
            $shipment->discount_name = $shipment->discount_name ?? 'Discount Applied';
        }
        
        return $shipment;
    }
    
    /**
     * Cancel an order
     */
    public function cancel(Request $request, $id)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|min:10|max:500',
        ]);

        $user = Auth::user();
        
        if (!$user) {
            Session::flash('error', 'Authentication required');
            return redirect()->back();
        }
        
        $shipment = Shipment::findOrFail($id);
        
        // Check permissions
        $isCustomer = $shipment->user_id === $user->id;
        $isVendor = false;
        
        if (!$isCustomer) {
            // Check if user is vendor for this shipment
            $product = Product::where('product_id', $shipment->product_id)->first();
            $isVendor = $product && $product->user_id == $user->id;
            
            if (!$isVendor) {
                Session::flash('error', 'Unauthorized action.');
                return redirect()->back();
            }
        }

        // Check if shipment can be cancelled
        if (!in_array($shipment->status, ['pending', 'processing'])) {
            Session::flash('error', 'Order cannot be cancelled at this stage.');
            return redirect()->back();
        }

        try {
            DB::beginTransaction();

            // Restore stock if product exists
            $product = Product::where('product_id', $shipment->product_id)->first();
            if ($product && $shipment->quantity) {
                $product->increment('stock', $shipment->quantity);
            }

            // Update shipment
            $shipment->update([
                'status' => 'cancelled',
                'cancellation_reason' => $request->cancellation_reason,
                'cancelled_at' => now(),
            ]);

            // Update payment if exists
            if ($shipment->payment_id) {
                $payment = Payment::find($shipment->payment_id);
                if ($payment) {
                    $payment->update(['status' => 'cancelled']);
                }
            }

            DB::commit();
            
            Session::flash('success', 'Order cancelled successfully.');
            
            // Redirect based on user type
            if ($isVendor) {
                return redirect()->route('orders.vendor');
            } else {
                return redirect()->route('orders.customer');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order cancellation failed', ['error' => $e->getMessage()]);
            Session::flash('error', 'Error cancelling order: ' . $e->getMessage());
            return redirect()->back();
        }
    }
    
    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user) {
            Session::flash('error', 'Authentication required');
            return redirect()->back();
        }
        
        // Check if user is verified vendor
        if (!$user->verified_at) {
            Session::flash('error', 'Only verified vendors can update order status.');
            return redirect()->back();
        }
        
        $shipment = Shipment::findOrFail($id);
        
        // Check if user is vendor for this shipment
        $product = Product::where('product_id', $shipment->product_id)->first();
        if (!$product || $product->user_id != $user->id) {
            Session::flash('error', 'Unauthorized action.');
            return redirect()->back();
        }

        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered'
        ]);

        // Don't allow changing to cancelled through this method
        if ($request->status === 'cancelled') {
            Session::flash('error', 'Please use the cancel order method to cancel orders.');
            return redirect()->back();
        }

        $shipment->update(['status' => $request->status]);
        
        // Update payment when delivered
        if ($request->status === 'delivered' && $shipment->payment_id) {
            $payment = Payment::find($shipment->payment_id);
            if ($payment) {
                $payment->update(['status' => 'completed']);
            }
        }
        
        Session::flash('success', 'Order status updated to ' . ucfirst($request->status) . ' successfully.');
        return redirect()->route('orders.vendor');
    }
}