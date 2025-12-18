<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CartController extends Controller
{
    public function index()
    {
        return Inertia::render('Cart/Index');
    }
    
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'quantity' => 'required|integer|min:1',
        ]);
        
        // Your cart logic here
        
        return redirect()->back()->with('success', 'Product added to cart!');
    }
    
    public function buyNow(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,product_id',
        ]);
        
        // Store product in session for payment page
        session(['buy_now_product' => $request->product_id]);
        
        return redirect()->route('payment.submit');
    }
}