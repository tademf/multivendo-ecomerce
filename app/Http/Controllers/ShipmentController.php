<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Models\Product;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // ADD THIS IMPORT
use Illuminate\Support\Str;

class ShipmentController extends Controller
{
    // Show shipment management page
    public function index()
    {
        $user = Auth::user();
        
        // Get all shipments with their data - FIXED to include customer fields
        $shipments = Shipment::with(['items', 'payment'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Log shipments for debugging
        Log::info('Shipments retrieved:', [
            'count' => $shipments->count(),
            'shipments' => $shipments->toArray()
        ]);
        
        // Calculate statistics
        $stats = [
            'total' => $shipments->count(),
            'pending' => $shipments->where('status', 'pending')->count(),
            'processing' => $shipments->where('status', 'processing')->count(),
            'shipped' => $shipments->where('status', 'shipped')->count(),
            'delivered' => $shipments->where('status', 'delivered')->count(),
            'cancelled' => $shipments->where('status', 'cancelled')->count(),
            'total_amount' => $shipments->sum('total_amount'),
        ];
        
        return Inertia::render('ShipmentPage', [
            'shipments' => $shipments,
            'stats' => $stats,
            'user' => $user,
        ]);
    }
    
    // Show form to create manual shipment
    public function create()
    {
        $user = Auth::user();
        
        $products = Product::where('user_id', $user->id)
            ->where('stock', '>', 0)
            ->get(['product_id', 'name', 'price', 'stock']);
        
        return Inertia::render('Shipments/Create', [
            'products' => $products,
        ]);
    }
    
    // Store manual shipment
    public function store(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'delivery_address' => 'required|string|max:500',
            'shipping_cost' => 'required|numeric|min:0',
            'carrier' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,product_id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Calculate total amount
            $itemsTotal = 0;
            $itemsData = [];
            
            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                
                if (!$product) {
                    return back()->withErrors([
                        'items' => 'Product not found.',
                    ]);
                }
                
                if ($product->stock < $item['quantity']) {
                    return back()->withErrors([
                        'items' => 'Insufficient stock for product: ' . $product->name,
                    ]);
                }
                
                $unitPrice = $product->price;
                $totalPrice = $item['quantity'] * $unitPrice;
                $itemsTotal += $totalPrice;
                
                $itemsData[] = [
                    'product_id' => $product->product_id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ];
                
                // Reduce product stock
                $product->decrement('stock', $item['quantity']);
            }
            
            $totalAmount = $itemsTotal + $validated['shipping_cost'];
            
            // Generate tracking number
            $trackingNumber = 'TRK-' . Str::upper(Str::random(8));
            
            // Create shipment WITH customer fields
            $shipment = Shipment::create([
                'user_id' => $user->id,
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'] ?? null,
                'delivery_address' => $validated['delivery_address'],
                'shipping_cost' => $validated['shipping_cost'],
                'carrier' => $validated['carrier'] ?? 'standard',
                'total_amount' => $totalAmount,
                'notes' => $validated['notes'] ?? null,
                'status' => 'pending',
                'tracking_number' => $trackingNumber,
            ]);
            
            // Create shipment items
            foreach ($itemsData as $itemData) {
                $shipment->items()->create($itemData);
            }
            
            DB::commit();
            
            return redirect()->route('shipments.index')
                ->with('success', 'Shipment created successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create shipment: ' . $e->getMessage());
        }
    }
    
    // Create shipment from payment (THIS IS THE KEY FUNCTION FOR PAYMENT PAGE)
    public function createFromPayment(Request $request)
    {
        $user = Auth::user();
        
        // Log incoming request
        Log::info('createFromPayment called with:', $request->all());
        
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'delivery_address' => 'required|string|max:500',
            'shipping_cost' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,product_id',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.total_price' => 'required|numeric|min:0',
            'payment_id' => 'nullable|exists:payments,id',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Generate tracking number
            $trackingNumber = 'TRK-' . Str::upper(Str::random(8));
            
            // Create shipment WITH customer fields
            $shipment = Shipment::create([
                'user_id' => $user->id,
                'payment_id' => $validated['payment_id'] ?? null,
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'] ?? null,
                'delivery_address' => $validated['delivery_address'],
                'status' => 'pending',
                'tracking_number' => $trackingNumber,
                'carrier' => 'standard',
                'shipping_cost' => $validated['shipping_cost'],
                'total_amount' => $validated['total_amount'],
                'notes' => $validated['notes'] ?? null,
            ]);
            
            // Create shipment items
            foreach ($validated['items'] as $item) {
                ShipmentItem::create([
                    'shipment_id' => $shipment->id,
                    'product_id' => $item['product_id'] ?? null,
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price'],
                ]);
                
                // Update product stock if product_id exists
                if (!empty($item['product_id'])) {
                    $product = Product::find($item['product_id']);
                    if ($product) {
                        $product->decrement('stock', $item['quantity']);
                    }
                }
            }
            
            DB::commit();
            
            // Log success
            Log::info('Shipment created from payment:', [
                'shipment_id' => $shipment->id,
                'tracking_number' => $trackingNumber,
                'customer_name' => $validated['customer_name']
            ]);
            
            // Generate order reference
            $orderReference = 'ORD-' . str_pad($shipment->id, 6, '0', STR_PAD_LEFT);
            
            return [
                'success' => true,
                'shipment_id' => $shipment->id,
                'order_reference' => $orderReference,
                'tracking_number' => $trackingNumber,
                'message' => 'Shipment created successfully!'
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log error
            Log::error('Failed to create shipment from payment:', [
                'error' => $e->getMessage(),
                'data' => $validated
            ]);
            
            return [
                'success' => false,
                'message' => 'Failed to create shipment: ' . $e->getMessage()
            ];
        }
    }
    
    // Show single shipment
    public function show(Shipment $shipment)
    {
        // Check if shipment belongs to user
        if ($shipment->user_id !== Auth::id()) {
            abort(403);
        }
        
        $shipment->load(['items.product', 'payment']);
        
        return Inertia::render('Shipments/Show', [
            'shipment' => $shipment,
        ]);
    }
    
    // Show edit form
    public function edit(Shipment $shipment)
    {
        // Check if shipment belongs to user and is pending
        if ($shipment->user_id !== Auth::id() || $shipment->status !== 'pending') {
            abort(403);
        }
        
        $shipment->load(['items.product']);
        $user = Auth::user();
        
        $products = Product::where('user_id', $user->id)
            ->where('stock', '>', 0)
            ->get(['product_id', 'name', 'price', 'stock']);
        
        return Inertia::render('Shipments/Edit', [
            'shipment' => $shipment,
            'products' => $products,
        ]);
    }
    
    // Update shipment
    public function update(Request $request, Shipment $shipment)
    {
        // Check if shipment belongs to user and is pending
        if ($shipment->user_id !== Auth::id() || $shipment->status !== 'pending') {
            abort(403);
        }
        
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'delivery_address' => 'required|string|max:500',
            'shipping_cost' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        // Recalculate total amount
        $itemsTotal = $shipment->items()->sum('total_price');
        $totalAmount = $itemsTotal + $validated['shipping_cost'];
        
        $shipment->update([
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'] ?? null,
            'delivery_address' => $validated['delivery_address'],
            'shipping_cost' => $validated['shipping_cost'],
            'total_amount' => $totalAmount,
            'notes' => $validated['notes'],
        ]);
        
        return redirect()->route('shipments.show', $shipment->id)
            ->with('success', 'Shipment updated successfully.');
    }
    
    // Delete shipment
    public function destroy(Shipment $shipment)
    {
        // Only pending shipments can be deleted
        if ($shipment->user_id !== Auth::id() || $shipment->status !== 'pending') {
            abort(403);
        }
        
        // Return stock to products
        foreach ($shipment->items as $item) {
            if ($item->product_id) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->increment('stock', $item->quantity);
                }
            }
        }
        
        $shipment->delete();
        
        return redirect()->route('shipments.index')
            ->with('success', 'Shipment deleted successfully.');
    }
    
    // Cancel shipment
    public function cancel(Request $request, Shipment $shipment)
    {
        // Check if shipment belongs to user and can be cancelled
        if ($shipment->user_id !== Auth::id() || 
            $shipment->status === 'cancelled' || 
            $shipment->status === 'delivered') {
            abort(403);
        }
        
        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);
        
        // Return stock to products
        foreach ($shipment->items as $item) {
            if ($item->product_id) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->increment('stock', $item->quantity);
                }
            }
        }
        
        $shipment->update([
            'status' => 'cancelled',
            'cancellation_reason' => $validated['cancellation_reason'],
            'cancelled_at' => now(),
        ]);
        
        return redirect()->route('shipments.show', $shipment->id)
            ->with('success', 'Shipment cancelled successfully.');
    }
    
    // Mark as shipped
    public function markAsShipped(Request $request, Shipment $shipment)
    {
        if ($shipment->user_id !== Auth::id()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'tracking_number' => 'nullable|string|max:100',
            'carrier' => 'nullable|string|max:100',
        ]);
        
        $shipment->update([
            'status' => 'shipped',
            'tracking_number' => $validated['tracking_number'] ?? $shipment->tracking_number,
            'carrier' => $validated['carrier'] ?? $shipment->carrier,
            'shipped_at' => now(),
        ]);
        
        return redirect()->route('shipments.show', $shipment->id)
            ->with('success', 'Shipment marked as shipped.');
    }
    
    // Mark as delivered
    public function markAsDelivered(Shipment $shipment)
    {
        if ($shipment->user_id !== Auth::id()) {
            abort(403);
        }
        
        $shipment->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
        
        return redirect()->route('shipments.show', $shipment->id)
            ->with('success', 'Shipment marked as delivered.');
    }
    
    // Update shipment status (PATCH method)
    public function updateStatus(Request $request, Shipment $shipment)
    {
        if ($shipment->user_id !== Auth::id()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'cancellation_reason' => 'nullable|string|required_if:status,cancelled',
            'tracking_number' => 'nullable|string',
            'carrier' => 'nullable|string',
        ]);
        
        $updateData = [
            'status' => $validated['status'],
        ];
        
        // Update timestamps based on status
        switch ($validated['status']) {
            case 'processing':
                break;
            case 'shipped':
                $updateData['shipped_at'] = now();
                break;
            case 'delivered':
                $updateData['delivered_at'] = now();
                break;
            case 'cancelled':
                $updateData['cancelled_at'] = now();
                $updateData['cancellation_reason'] = $validated['cancellation_reason'] ?? null;
                
                // Restore product stock if cancelled
                foreach ($shipment->items as $item) {
                    if ($item->product_id) {
                        $product = Product::find($item->product_id);
                        if ($product) {
                            $product->increment('stock', $item->quantity);
                        }
                    }
                }
                break;
        }
        
        if (!empty($validated['tracking_number'])) {
            $updateData['tracking_number'] = $validated['tracking_number'];
        }
        
        if (!empty($validated['carrier'])) {
            $updateData['carrier'] = $validated['carrier'];
        }
        
        $shipment->update($updateData);
        
        return redirect()->back()->with('success', 'Shipment status updated successfully.');
    }
    
    // Get shipment statistics (API endpoint if needed)
    public function statistics()
    {
        $user = Auth::user();
        
        $shipments = Shipment::where('user_id', $user->id)->get();
        
        $stats = [
            'total' => $shipments->count(),
            'pending' => $shipments->where('status', 'pending')->count(),
            'processing' => $shipments->where('status', 'processing')->count(),
            'shipped' => $shipments->where('status', 'shipped')->count(),
            'delivered' => $shipments->where('status', 'delivered')->count(),
            'cancelled' => $shipments->where('status', 'cancelled')->count(),
            'total_amount' => $shipments->sum('total_amount'),
        ];
        
        return response()->json($stats);
    }
}