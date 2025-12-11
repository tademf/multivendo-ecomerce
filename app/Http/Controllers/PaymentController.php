<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function process(Request $request)
    {
        // Debug 1: Check what's coming in
        Log::info('=== PAYMENT PROCESS START ===');
        Log::info('Request data:', $request->all());
        Log::info('Files:', $request->files->all());
        
        // Validate the request - ADD PRODUCT INFO VALIDATION
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'shipment_address' => 'required|string',
            'payment_image' => 'required|image|max:5120',
            'product_id' => 'required|exists:products,product_id',
            'quantity' => 'required|integer|min:1',
            'product_name' => 'required|string|max:255',
            'product_image' => 'nullable|string',
        ]);
        
        Log::info('Validation passed:', $validated);
        
        // Check product availability
        $product = Product::find($request->product_id);
        if (!$product) {
            return redirect()->back()->with('error', 'Product not found.');
        }
        
        if (!$product->isAvailable($request->quantity)) {
            return redirect()->back()->with('error', 'Insufficient stock for this product.');
        }
        
        try {
            DB::beginTransaction();
            
            // Store payment image
            $imagePath = null;
            if ($request->hasFile('payment_image')) {
                $imagePath = $request->file('payment_image')->store('payment-screenshots', 'public');
                Log::info('Image stored at: ' . $imagePath);
            }
            
            // Deduct product stock
            $product->updateStock($request->quantity, 'sale');
            
            // Create payment with product info
            $payment = Payment::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'product_name' => $request->product_name,
                'product_image' => $request->product_image,
                'name' => $request->name,
                'amount' => $request->amount,
                'quantity' => $request->quantity,
                'shipment_address' => $request->shipment_address,
                'payment_image' => $imagePath,
                'status' => 'pending',
                'order_reference' => 'PAY-' . Str::random(10) . time(),
            ]);
            
            Log::info('Payment created successfully:', $payment->toArray());
            
            // Create order with product info
            $order = Order::create([
                'user_id' => Auth::id(),
                'payment_id' => $payment->id,
                'product_id' => $request->product_id,
                'product_name' => $request->product_name,
                'product_image' => $request->product_image,
                'name' => $request->name,
                'amount' => $request->amount,
                'quantity' => $request->quantity,
                'shipment_address' => $request->shipment_address,
                'payment_image' => $imagePath,
                'status' => 'pending',
                'order_number' => 'ORD-' . Str::random(8) . time(),
            ]);
            
            Log::info('Order created successfully:', $order->toArray());
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment/Order creation failed:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Payment failed: ' . $e->getMessage());
        }
        
        Log::info('=== PAYMENT PROCESS COMPLETE ===');
        
        // Redirect to orders page
        return redirect()->route('orders.index')->with('success', 'Payment submitted successfully!');
    }
}