<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PaymentController extends Controller
{
    /**
     * Show the payment form using Inertia (Vue component)
     */
    public function show(Request $request)
    {
        try {
            Log::info('=== PAYMENT FORM REQUEST ===', ['request_data' => $request->all()]);
            
            // Get product info from query parameters
            $productId = $request->get('product_id');
            $quantity = $request->get('quantity', 1);
            $price = $request->get('price');
            $productName = $request->get('product_name');
            $productImage = $request->get('product_image');
            $stock = $request->get('stock');
            
            // Initialize productData with defaults
            $productData = [
                'product_id' => null,
                'product_name' => '',
                'product_image' => '',
                'price' => 0,
                'quantity' => 1,
                'stock' => null
            ];
            
            $vendorAccountNumber = null;
            
            // If we have a product ID, fetch the product
            if ($productId) {
                $product = Product::with('user')->find($productId);
                
                if ($product) {
                    $productData = [
                        'product_id' => $product->product_id,
                        'product_name' => $productName ?: $product->name,
                        'product_image' => $productImage ?: $product->image,
                        'price' => $price ?: $product->price,
                        'quantity' => $quantity,
                        'stock' => $stock ?: $product->stock,
                    ];
                    
                    // Get vendor account number FROM USERS TABLE
                    if ($product->user) {
                        $vendor = $product->user;
                        $vendorAccountNumber = $vendor->account_number;
                        
                        if (!$vendorAccountNumber) {
                            Log::warning('Vendor has no account number in users table:', ['vendor_id' => $vendor->id]);
                        } else {
                            Log::info('Vendor account number fetched from users table:', [
                                'vendor_id' => $vendor->id,
                                'account_number' => $vendorAccountNumber
                            ]);
                        }
                    } else {
                        Log::warning('Product has no associated user/vendor:', ['product_id' => $productId]);
                    }
                    
                    // Validate quantity
                    if ($quantity > $product->stock) {
                        return back()->with('error', 'Requested quantity exceeds available stock. Available: ' . $product->stock);
                    }
                } else {
                    Log::warning('Product not found in database, using query params:', ['product_id' => $productId]);
                    $productData = [
                        'product_id' => $productId,
                        'product_name' => $productName ?: 'Unknown Product',
                        'product_image' => $productImage ?: '',
                        'price' => $price ?: 0,
                        'quantity' => $quantity,
                        'stock' => $stock ?: 0,
                    ];
                }
            } else {
                Log::warning('No product_id provided in payment form request');
            }
            
            // Get current user info
            $user = Auth::user();
            
            if (!$user) {
                Log::warning('No authenticated user for payment form');
                return redirect()->route('login')->with('error', 'Please login to continue with payment.');
            }
            
            Log::info('Payment form data prepared:', [
                'product_data' => $productData,
                'user_id' => $user->id,
                'vendor_account_number' => $vendorAccountNumber,
            ]);
            
            // Render payment page
            return Inertia::render('PaymentPage', [
                'productData' => $productData,
                'vendorAccountNumber' => $vendorAccountNumber,
                'user' => $user,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error showing payment form:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return redirect()->route('home')
                ->with('error', 'Unable to load payment form. Please try again.');
        }
    }
    
    /**
     * Process payment submission and STORE IN PAYMENTS & ORDERS TABLES
     */
    public function process(Request $request)
    {
        Log::info('=== PAYMENT PROCESS START ===');
        Log::info('User ID:', ['user_id' => Auth::id()]);
        Log::info('Request data keys:', array_keys($request->except(['payment_image'])));
        Log::info('Has payment_image file:', ['has_file' => $request->hasFile('payment_image')]);
        
        // Start database transaction
        DB::beginTransaction();
        
        try {
            // Validate the request
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'amount' => 'required|numeric|min:0.01',
                'shipment_address' => 'required|string|max:500',
                'payment_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
                'product_id' => 'required',
                'product_name' => 'required|string|max:255',
                'product_image' => 'nullable|string|max:500',
                'quantity' => 'required|integer|min:1',
            ]);
            
            Log::info('Validation passed successfully');
            
            // Check if user is authenticated
            $user = Auth::user();
            if (!$user) {
                throw new \Exception('User not authenticated');
            }
            
            // Check if product exists
            $product = Product::with('user')->find($request->product_id);
            
            if (!$product) {
                Log::error('Product not found:', ['product_id' => $request->product_id]);
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Product not found.');
            }
            
            Log::info('Product found:', [
                'product_id' => $product->product_id,
                'product_name' => $product->name,
                'current_stock' => $product->stock
            ]);
            
            // Get vendor information FROM USERS TABLE
            $vendor = $product->user;
            $vendorId = null;
            $vendorAccountNumber = null;
            
            if ($vendor) {
                $vendorId = $vendor->id;
                // GET VENDOR ACCOUNT NUMBER FROM USERS TABLE
                $vendorAccountNumber = $vendor->account_number;
                
                Log::info('Vendor info from users table:', [
                    'vendor_id' => $vendorId,
                    'vendor_name' => $vendor->name ?? $vendor->full_name ?? 'Unknown',
                    'account_number' => $vendorAccountNumber
                ]);
            } else {
                Log::warning('Product has no associated vendor/user', ['product_id' => $product->product_id]);
            }
            
            // Check stock availability
            if ($product->stock < $request->quantity) {
                Log::error('Insufficient stock:', [
                    'product_id' => $product->product_id,
                    'requested' => $request->quantity,
                    'available' => $product->stock
                ]);
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Insufficient stock. Available: ' . $product->stock);
            }
            
            // Store payment image
            $paymentImagePath = null;
            if ($request->hasFile('payment_image')) {
                $imageFile = $request->file('payment_image');
                // Generate unique filename
                $filename = 'payment_' . time() . '_' . Str::random(10) . '.' . $imageFile->getClientOriginalExtension();
                $paymentImagePath = $imageFile->storeAs('payment-screenshots', $filename, 'public');
                Log::info('Payment image stored:', ['path' => $paymentImagePath]);
            } else {
                throw new \Exception('Payment screenshot is required');
            }
            
            // Get product image path
            $productImage = $product->image ?: ($request->product_image ?: 'default-product.jpg');
            
            // Deduct product stock
            $oldStock = $product->stock;
            $product->stock -= $request->quantity;
            $product->save();
            
            Log::info('Stock updated:', [
                'product_id' => $product->product_id,
                'old_stock' => $oldStock,
                'new_stock' => $product->stock,
                'quantity_sold' => $request->quantity
            ]);
            
            // Generate unique references
            $orderReference = 'PAY-' . Str::upper(Str::random(10)) . '-' . time();
            $orderNumber = 'ORD-' . Str::upper(Str::random(8)) . '-' . time();
            
            // ============================================
            // ✅ STORE PAYMENT IN PAYMENTS TABLE
            // ============================================
            Log::info('Creating payment record...');
            
            $paymentData = [
                'user_id' => $user->id,
                'product_id' => $product->product_id,
                'product_name' => $product->name,
                'product_image' => $productImage,
                'name' => $request->name,
                'payment_image' => $paymentImagePath,
                'amount' => $request->amount,
                'quantity' => $request->quantity,
                'shipment_address' => $request->shipment_address,
                'status' => 'pending',
                'order_reference' => $orderReference,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            // Add email if provided
            if ($request->email) {
                $paymentData['email'] = $request->email;
            }
            
            // Add vendor account number FROM USERS TABLE
            if ($vendorAccountNumber) {
                $paymentData['vendor_account_number'] = $vendorAccountNumber;
                Log::info('Added vendor account number to payment:', ['account_number' => $vendorAccountNumber]);
            }
            
            $payment = Payment::create($paymentData);
            
            Log::info('✅ PAYMENT STORED SUCCESSFULLY', [
                'payment_id' => $payment->id,
                'order_reference' => $payment->order_reference,
                'user_id' => $payment->user_id,
                'amount' => $payment->amount
            ]);
            
            // ============================================
            // ✅ CREATE ORDER RECORD IN ORDERS TABLE
            // ============================================
            Log::info('Creating order record...');
            
            // Check orders table schema and add necessary fields
            $orderData = [
                'user_id' => $user->id,
                'payment_id' => $payment->id, // Link to payment
                'product_id' => $product->product_id,
                'product_name' => $product->name,
                'product_image' => $productImage,
                'name' => $request->name,
                'email' => $request->email,
                'amount' => $request->amount,
                'quantity' => $request->quantity,
                'shipment_address' => $request->shipment_address,
                'status' => 'pending',
                'order_number' => $orderNumber,
                'payment_method' => 'manual_upload',
                'payment_status' => 'pending',
                'payment_reference' => $orderReference,
                'vendor_id' => $vendorId,
                'vendor_account_number' => $vendorAccountNumber,
                'notes' => 'Payment reference: ' . $orderReference,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            // Add cancellation_reason field if your orders table has it
            if ($vendorAccountNumber) {
                $orderData['cancellation_reason'] = 'Product: ' . $product->name . 
                                                   ' | Product ID: ' . $product->product_id . 
                                                   ' | Quantity: ' . $request->quantity . 
                                                   ' | Vendor ID: ' . $vendorId . 
                                                   ' | Vendor Account: ' . $vendorAccountNumber;
            }
            
            $order = Order::create($orderData);
            
            Log::info('✅ ORDER CREATED SUCCESSFULLY', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'payment_id' => $order->payment_id,
                'vendor_id' => $order->vendor_id,
                'vendor_account_number' => $order->vendor_account_number
            ]);
            
            // Check if product should be marked as inactive
            if ($product->stock <= 0 && ($product->auto_delete_out_of_stock ?? false)) {
                $product->status = 'inactive';
                $product->save();
                Log::info('Product marked as inactive due to zero stock', ['product_id' => $product->product_id]);
            }
            
            // Commit transaction
            DB::commit();
            
            Log::info('=== PAYMENT PROCESS COMPLETED SUCCESSFULLY ===');
            Log::info('Summary:', [
                'payment_id' => $payment->id,
                'order_id' => $order->id,
                'user_id' => $user->id,
                'product_id' => $product->product_id,
                'total_amount' => $request->amount,
                'order_reference' => $orderReference,
                'vendor_account_number' => $vendorAccountNumber
            ]);
            
            // Redirect to customer orders page
            return redirect()->route('orders.customer')
                ->with('success', 'Payment submitted successfully! Your order #' . $orderNumber . ' is being processed.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation failed:', ['errors' => $e->errors()]);
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment/Order creation failed:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'product_id' => $request->product_id ?? 'unknown'
            ]);
            
            $errorMessage = 'Payment failed: ' . $e->getMessage();
            if (str_contains($e->getMessage(), 'SQLSTATE')) {
                $errorMessage = 'Database error occurred. Please try again or contact support.';
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', $errorMessage);
        }
    }
    
    /**
     * Debug endpoint to check if payments are being stored
     */
    public function debugPayments()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $payments = Payment::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
            
        $orders = Order::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
            
        return response()->json([
            'payments_count' => $payments->count(),
            'orders_count' => $orders->count(),
            'payments' => $payments,
            'orders' => $orders,
            'user_id' => Auth::id()
        ]);
    }
}