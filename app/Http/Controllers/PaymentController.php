<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Shipment;
use App\Models\Product;
use App\Models\Discount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Support\Facades\Schema;

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
            $discountId = $request->get('discount_id');
            $productImage = $request->get('product_image'); // Get the image passed from product page
            
            Log::info('Payment form parameters:', [
                'product_id' => $productId,
                'discount_id' => $discountId,
                'quantity' => $quantity,
                'product_image_passed' => $productImage ? 'YES' : 'NO'
            ]);
            
            // Initialize productData with defaults
            $productData = [
                'product_id' => null,
                'product_name' => '',
                'product_image' => '',
                'unit_price' => 0,
                'quantity' => 1,
                'stock' => null,
                'is_discounted' => false,
                'discount_id' => null,
                'original_price' => 0,
                'discount_name' => null,
                'total_amount' => 0
            ];
            
            $productOwnerAccountNumber = null;
            
            // If we have a product ID, fetch the product
            if ($productId) {
                $product = Product::with(['user'])->find($productId);
                
                if ($product) {
                    // ✅ CHECK IF USER IS TRYING TO BUY THEIR OWN PRODUCT
                    if (Auth::check() && Auth::id() == $product->user_id) {
                        Log::warning('User tried to buy their own product', [
                            'user_id' => Auth::id(),
                            'product_id' => $productId,
                            'product_owner_id' => $product->user_id
                        ]);
                        return redirect()->back()->with('error', 'You cannot buy your own product!');
                    }
                    
                    $originalPrice = $product->price;
                    $unitPrice = $originalPrice; // Start with original price
                    $isDiscounted = false;
                    $activeDiscountId = null;
                    $discountName = null;
                    
                    Log::info('Product price details:', [
                        'original_price' => $originalPrice,
                        'stock' => $product->stock
                    ]);
                    
                    // ✅ STEP 1: Check if discount_id is provided in query
                    if ($discountId) {
                        Log::info('Discount ID provided in query:', ['discount_id' => $discountId]);
                        
                        $discount = Discount::where('discount_id', $discountId)
                            ->where('product_id', $productId)
                            ->where('status', 'active')
                            ->where('end_date', '>', Carbon::now())
                            ->first();
                        
                        if ($discount) {
                            $isDiscounted = true;
                            $discountPercentage = floatval($discount->discount_amount);
                            $unitPrice = $originalPrice - ($originalPrice * $discountPercentage / 100);
                            $activeDiscountId = $discountId;
                            $discountName = $discount->discount_name;
                            
                            Log::info('✅ Discount applied from query:', [
                                'discount_id' => $discountId,
                                'discount_name' => $discount->discount_name,
                                'original_price' => $originalPrice,
                                'discount_percent' => $discountPercentage . '%',
                                'unit_price' => $unitPrice,
                                'savings' => ($originalPrice - $unitPrice)
                            ]);
                        } else {
                            Log::warning('❌ Discount not found or not active:', [
                                'discount_id' => $discountId,
                                'product_id' => $productId
                            ]);
                        }
                    }
                    // ✅ STEP 2: Check if product has any active discount
                    else {
                        $discount = Discount::where('product_id', $productId)
                            ->where('status', 'active')
                            ->where('end_date', '>', Carbon::now())
                            ->first();
                        
                        if ($discount) {
                            Log::info('Product has active discount:', [
                                'discount_id' => $discount->discount_id,
                                'discount_name' => $discount->discount_name
                            ]);
                            
                            $isDiscounted = true;
                            $discountPercentage = floatval($discount->discount_amount);
                            $unitPrice = $originalPrice - ($originalPrice * $discountPercentage / 100);
                            $activeDiscountId = $discount->discount_id;
                            $discountName = $discount->discount_name;
                            
                            Log::info('✅ Product has active discount in DB:', [
                                'discount_id' => $discount->discount_id,
                                'discount_name' => $discount->discount_name,
                                'original_price' => $originalPrice,
                                'discount_percent' => $discountPercentage . '%',
                                'unit_price' => $unitPrice,
                                'savings' => ($originalPrice - $unitPrice)
                            ]);
                        }
                    }
                    
                    // Calculate total amount
                    $totalAmount = $unitPrice * $quantity;
                    
                    Log::info('Final price calculation:', [
                        'original_price' => $originalPrice,
                        'unit_price' => $unitPrice,
                        'quantity' => $quantity,
                        'total_amount' => $totalAmount,
                        'is_discounted' => $isDiscounted ? 'YES' : 'NO'
                    ]);
                    
                    // ✅ USE THE PASSED IMAGE FROM PRODUCT PAGE (not product->image)
                    $productImageToUse = $productImage ?: $product->image;
                    
                    Log::info('Image selection:', [
                        'passed_image' => $productImage,
                        'product_main_image' => $product->image,
                        'image_to_use' => $productImageToUse
                    ]);
                    
                    $productData = [
                        'product_id' => $product->product_id,
                        'product_name' => $product->name,
                        'product_image' => $productImageToUse, // Use the passed image
                        'unit_price' => $unitPrice,
                        'quantity' => $quantity,
                        'stock' => $product->stock,
                        'is_discounted' => $isDiscounted,
                        'discount_id' => $activeDiscountId,
                        'original_price' => $originalPrice,
                        'total_amount' => $totalAmount,
                        'discount_name' => $discountName,
                    ];
                    
                    // Get product owner (user) account number from users table
                    if ($product->user && $product->user->account_number) {
                        $productOwnerAccountNumber = $product->user->account_number;
                        
                        Log::info('Product owner account number:', [
                            'product_owner_id' => $product->user->id,
                            'account_number' => $productOwnerAccountNumber
                        ]);
                    } else {
                        Log::warning('Product owner has no account number:', ['product_owner_id' => $product->user->id ?? 'unknown']);
                    }
                    
                    // Validate quantity
                    if ($quantity > $product->stock) {
                        return back()->with('error', 'Requested quantity exceeds available stock. Available: ' . $product->stock);
                    }
                } else {
                    Log::warning('Product not found in database:', ['product_id' => $productId]);
                    return redirect()->back()->with('error', 'Product not found.');
                }
            } else {
                Log::warning('No product_id provided in payment form request');
                return redirect()->back()->with('error', 'Product ID is required.');
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
                'product_owner_account_number' => $productOwnerAccountNumber,
                'is_discounted' => $productData['is_discounted'],
                'original_price' => $productData['original_price'],
                'unit_price' => $productData['unit_price'],
                'total_amount' => $productData['total_amount'],
                'product_image_used' => $productData['product_image']
            ]);
            
            // Render payment page
            return Inertia::render('PaymentPage', [
                'productData' => $productData,
                'vendorAccountNumber' => $productOwnerAccountNumber,
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
     * Process payment submission and STORE IN PAYMENTS & SHIPMENTS TABLES
     */
    public function process(Request $request)
    {
        Log::info('=== PAYMENT PROCESS START ===');
        Log::info('User ID:', ['user_id' => Auth::id()]);
        Log::info('Request data:', $request->except(['payment_image']));
        
        // Start database transaction
        DB::beginTransaction();
        
        try {
            // ✅ VALIDATION
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0.01', // This is TOTAL amount
                'shipment_address' => 'required|string|max:500',
                'payment_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
                'product_id' => 'required',
                'product_name' => 'required|string|max:255',
                'product_image' => 'nullable|string|max:500',
                'quantity' => 'required|integer|min:1',
                'is_discounted' => 'nullable|boolean',
                'discount_id' => 'nullable|string|max:255',
                'original_price' => 'required|numeric|min:0', // Original UNIT price
                'discount_name' => 'nullable|string|max:255',
                'discounted_price' => 'nullable|numeric',
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
                'current_stock' => $product->stock,
                'product_owner_id' => $product->user_id,
                'buyer_id' => $user->id
            ]);
            
            // ✅ CHECK IF USER IS BUYING THEIR OWN PRODUCT
            if ($user->id == $product->user_id) {
                Log::warning('User tried to buy their own product', [
                    'user_id' => $user->id,
                    'product_id' => $product->product_id,
                    'product_owner_id' => $product->user_id
                ]);
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'You cannot buy your own product!');
            }
            
            // Get product owner (user who owns the product)
            $productOwner = $product->user;
            $productOwnerAccountNumber = null;
            
            if ($productOwner) {
                $productOwnerAccountNumber = $productOwner->account_number;
                
                Log::info('Product owner info:', [
                    'product_owner_id' => $productOwner->id,
                    'product_owner_name' => $productOwner->name ?? $productOwner->full_name ?? 'Unknown',
                    'account_number' => $productOwnerAccountNumber
                ]);
            } else {
                Log::warning('Product has no associated owner', ['product_id' => $product->product_id]);
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
            
            // Check if this is a discounted purchase
            $isDiscounted = $request->has('is_discounted') && $request->is_discounted;
            $discountId = $request->discount_id;
            $originalPrice = $request->original_price; // Original UNIT price
            $quantity = $request->quantity;
            $totalAmount = $request->amount; // This is the TOTAL amount
            $discountName = $request->discount_name;
            
            // Calculate unit price from total amount
            $unitPrice = $totalAmount / $quantity;
            
            Log::info('Order details:', [
                'is_discounted' => $isDiscounted ? 'YES' : 'NO',
                'discount_id' => $discountId,
                'discount_name' => $discountName,
                'original_unit_price' => $originalPrice,
                'calculated_unit_price' => $unitPrice,
                'quantity' => $quantity,
                'total_amount' => $totalAmount,
                'savings_per_unit' => $isDiscounted ? ($originalPrice - $unitPrice) : 0
            ]);
            
            // ✅ IMPORTANT: Verify the amount is valid
            if ($totalAmount <= 0) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Invalid amount.');
            }
            
            // If discounted, verify discount exists and is active
            $discount = null;
            if ($isDiscounted && $discountId) {
                $discount = Discount::where('discount_id', $discountId)
                    ->where('product_id', $product->product_id)
                    ->where('status', 'active')
                    ->where('end_date', '>', Carbon::now())
                    ->first();
                
                if (!$discount) {
                    Log::warning('Discount validation failed:', [
                        'discount_id' => $discountId,
                        'product_id' => $product->product_id,
                        'now' => Carbon::now()->toDateTimeString()
                    ]);
                    
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Discount is no longer valid. Please refresh the page and try again.');
                }
                
                // Verify the discounted price matches discount percentage
                $expectedDiscount = $originalPrice * floatval($discount->discount_amount) / 100;
                $expectedUnitPrice = $originalPrice - $expectedDiscount;
                $expectedTotalAmount = $expectedUnitPrice * $quantity;
                
                // Allow small difference for floating point calculations (5% tolerance)
                $difference = abs($totalAmount - $expectedTotalAmount);
                $tolerance = $expectedTotalAmount * 0.05; // 5% tolerance
                
                if ($difference > $tolerance && $difference > 0.01) {
                    Log::warning('Price mismatch:', [
                        'provided_total' => $totalAmount,
                        'expected_total' => $expectedTotalAmount,
                        'difference' => $totalAmount - $expectedTotalAmount,
                        'tolerance' => $tolerance
                    ]);
                    
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Price calculation mismatch. Please refresh and try again.');
                }
                
                Log::info('✅ Discount validated:', [
                    'discount_id' => $discountId,
                    'discount_name' => $discount->discount_name,
                    'discount_percent' => $discount->discount_amount . '%',
                    'original_price' => $originalPrice,
                    'unit_price' => $unitPrice,
                    'savings_per_unit' => ($originalPrice - $unitPrice)
                ]);
            } else if ($isDiscounted) {
                // If marked as discounted but no discount_id, check if product has active discount
                $discount = Discount::where('product_id', $product->product_id)
                    ->where('status', 'active')
                    ->where('end_date', '>', Carbon::now())
                    ->first();
                
                if ($discount) {
                    $discountId = $discount->discount_id;
                    $discountName = $discount->discount_name;
                }
            }
            
            // Store payment image
            $paymentImagePath = null;
            if ($request->hasFile('payment_image')) {
                $imageFile = $request->file('payment_image');
                $filename = 'payment_' . time() . '_' . Str::random(10) . '.' . $imageFile->getClientOriginalExtension();
                $paymentImagePath = $imageFile->storeAs('payment-screenshots', $filename, 'public');
                Log::info('Payment image stored:', ['path' => $paymentImagePath]);
            } else {
                throw new \Exception('Payment screenshot is required');
            }
            
            // ✅ IMPORTANT: Use the product_image from request (selected image from product page)
            $productImage = $request->product_image ?: ($product->image ?: 'default-product.jpg');
            
            Log::info('Product image to save:', [
                'from_request' => $request->product_image,
                'from_product' => $product->image,
                'final_image' => $productImage
            ]);
            
            // Generate unique references
            $orderReference = 'PAY-' . Str::upper(Str::random(10)) . '-' . time();
            $orderNumber = 'ORD-' . Str::upper(Str::random(8)) . '-' . time();
            
            // Calculate total original price for this order
            $totalOriginalPrice = $originalPrice * $quantity;
            
            // ============================================
            // ✅ STEP 1: CREATE PAYMENT RECORD
            // ============================================
            $paymentData = [
                'user_id' => $user->id,
                'product_id' => $product->product_id,
                'product_name' => $product->name,
                'product_image' => $productImage, // Use the selected image
                'name' => $request->name,
                'payment_image' => $paymentImagePath,
                'amount' => $totalAmount,
                'quantity' => $quantity,
                'shipment_address' => $request->shipment_address,
                'status' => 'pending',
                'order_reference' => $orderReference,
                'is_discounted' => $isDiscounted,
                'original_price' => $totalOriginalPrice,
            ];
            
            // Add discount information if applicable
            if ($isDiscounted && $discountId) {
                $paymentData['discount_id'] = $discountId;
                $paymentData['discount_name'] = $discountName;
            }
            
            Log::info('Creating payment with data:', $paymentData);
            
            $payment = Payment::create($paymentData);
            
            Log::info('✅ PAYMENT STORED SUCCESSFULLY', [
                'payment_id' => $payment->id,
                'order_reference' => $payment->order_reference,
                'amount' => $payment->amount,
                'is_discounted' => $isDiscounted ? 'YES' : 'NO',
                'product_image_saved' => $payment->product_image
            ]);
            
            // ============================================
            // ✅ STEP 2: DEDUCT PRODUCT STOCK
            // ============================================
            $oldStock = $product->stock;
            $product->stock -= $quantity;
            $product->save();
            
            Log::info('Stock updated:', [
                'product_id' => $product->product_id,
                'old_stock' => $oldStock,
                'new_stock' => $product->stock,
                'quantity_sold' => $quantity
            ]);
            
            // ============================================
            // ✅ STEP 3: CREATE SHIPMENT RECORD
            // ============================================
            $shipmentData = [
                'user_id' => $user->id,
                'payment_id' => $payment->id,
                'product_id' => $product->product_id,
                'product_name' => $product->name,
                'product_image' => $productImage, // Use the selected image
                'name' => $request->name,
                'amount' => $totalAmount,
                'quantity' => $quantity,
                'shipment_address' => $request->shipment_address,
                'payment_image' => $paymentImagePath,
                'status' => 'pending',
                'cancellation_reason' => null,
                'order_number' => $orderNumber,
                'is_discounted' => $isDiscounted,
                'original_price' => $totalOriginalPrice,
            ];
            
            // Add discount information to shipment if applicable
            if ($isDiscounted && $discountId) {
                $shipmentData['discount_id'] = $discountId;
                $shipmentData['discount_name'] = $discountName;
            }
            
            Log::info('Creating shipment with data:', $shipmentData);
            
            $shipment = Shipment::create($shipmentData);
            
            Log::info('✅ SHIPMENT CREATED SUCCESSFULLY', [
                'shipment_id' => $shipment->id,
                'order_number' => $shipment->order_number,
                'amount' => $shipment->amount,
                'is_discounted' => $isDiscounted ? 'YES' : 'NO',
                'product_image_saved' => $shipment->product_image
            ]);
            
            // Check if product should be marked as inactive
            if ($product->stock <= 0) {
                $product->status = 'inactive';
                $product->save();
                Log::info('Product marked as inactive due to zero stock', ['product_id' => $product->product_id]);
            }
            
            // Commit transaction
            DB::commit();
            
            Log::info('=== PAYMENT PROCESS COMPLETED SUCCESSFULLY ===');
            Log::info('Summary:', [
                'payment_id' => $payment->id,
                'shipment_id' => $shipment->id,
                'user_id' => $user->id,
                'product_id' => $product->product_id,
                'unit_price' => $unitPrice . ' Birr',
                'quantity' => $quantity,
                'paid_amount' => $totalAmount . ' Birr',
                'is_discounted' => $isDiscounted ? 'YES' : 'NO',
                'original_price_total' => $totalOriginalPrice . ' Birr',
                'savings' => $isDiscounted ? ($totalOriginalPrice - $totalAmount) . ' Birr' : '0 Birr',
                'order_reference' => $orderReference,
                'order_number' => $orderNumber,
                'product_image_used' => $productImage
            ]);
            
            // ✅ Redirect to customer orders page
            $successMessage = 'Payment submitted successfully! Your order #' . $orderNumber . ' is being processed.';
            
            return redirect()->route('orders.customer')
                ->with('success', $successMessage)
                ->with('order_number', $orderNumber);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation failed:', ['errors' => $e->errors()]);
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors())
                ->with('error', 'Validation failed. Please check your input.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment/Shipment creation failed:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'product_id' => $request->product_id ?? 'unknown'
            ]);
            
            $errorMessage = 'Payment failed: ' . $e->getMessage();
            if (str_contains($e->getMessage(), 'SQLSTATE')) {
                $errorMessage = 'Database error occurred. Please try again.';
                Log::error('Database error details:', ['sql_error' => $e->getMessage()]);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', $errorMessage);
        }
    }
    
    /**
     * Get discount details for a product
     */
    public function getDiscountDetails(Request $request)
    {
        try {
            $productId = $request->get('product_id');
            $discountId = $request->get('discount_id');
            
            if (!$productId) {
                return response()->json([
                    'error' => 'Product ID is required'
                ], 400);
            }
            
            $product = Product::find($productId);
            
            if (!$product) {
                return response()->json([
                    'error' => 'Product not found'
                ], 404);
            }
            
            $originalPrice = $product->price;
            $unitPrice = $originalPrice;
            $isDiscounted = false;
            $discount = null;
            
            // If discount_id provided, get that specific discount
            if ($discountId) {
                $discount = Discount::where('discount_id', $discountId)
                    ->where('product_id', $productId)
                    ->where('status', 'active')
                    ->where('end_date', '>', Carbon::now())
                    ->first();
            } else {
                // Get first active discount for product
                $discount = Discount::where('product_id', $productId)
                    ->where('status', 'active')
                    ->where('end_date', '>', Carbon::now())
                    ->first();
            }
            
            if ($discount) {
                $isDiscounted = true;
                $discountPercentage = floatval($discount->discount_amount);
                $unitPrice = $originalPrice - ($originalPrice * $discountPercentage / 100);
            }
            
            return response()->json([
                'success' => true,
                'product' => [
                    'product_id' => $product->product_id,
                    'product_name' => $product->name,
                    'original_price' => $originalPrice,
                    'unit_price' => $unitPrice,
                    'is_discounted' => $isDiscounted,
                    'discount_id' => $discount ? $discount->discount_id : null,
                    'discount_name' => $discount ? $discount->discount_name : null,
                    'discount_percentage' => $discount ? $discount->discount_amount : 0,
                    'savings' => $isDiscounted ? ($originalPrice - $unitPrice) : 0
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Get discount details error:', [
                'message' => $e->getMessage()
            ]);
            
            return response()->json([
                'error' => 'Failed to get discount details'
            ], 500);
        }
    }
    
    /**
     * Debug endpoint to check database
     */
    public function debug()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        try {
            $user = Auth::user();
            
            // Get user info
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'account_number' => $user->account_number,
            ];
            
            // Check discount functionality
            $testProductId = Product::first()?->product_id;
            $activeDiscount = null;
            
            if ($testProductId) {
                $activeDiscount = Discount::where('product_id', $testProductId)
                    ->where('status', 'active')
                    ->where('end_date', '>', Carbon::now())
                    ->first();
            }
            
            return response()->json([
                'user' => $userData,
                'discount_test' => [
                    'test_product_id' => $testProductId,
                    'has_active_discount' => $activeDiscount ? 'YES' : 'NO',
                    'active_discount' => $activeDiscount ? [
                        'discount_id' => $activeDiscount->discount_id,
                        'discount_name' => $activeDiscount->discount_name,
                        'discount_percentage' => $activeDiscount->discount_amount
                    ] : null
                ],
                'database_status' => [
                    'payments_table_exists' => Schema::hasTable('payments'),
                    'shipments_table_exists' => Schema::hasTable('shipments'),
                    'discounts_table_exists' => Schema::hasTable('discounts'),
                    'products_table_exists' => Schema::hasTable('products'),
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}