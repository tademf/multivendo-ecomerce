<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\Order; // ADD THIS
use App\Models\Shipment;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth; // ADD THIS

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user(); // FIXED
        
        $stats = [
            'total_products' => Product::where('user_id', $user->id)->count(),
            'total_orders' => Order::where('user_id', $user->id)->count(),
            'total_wishlist' => Wishlist::where('user_id', $user->id)->count(),
            'recent_products' => Product::where('user_id', $user->id)
                ->latest()
                ->limit(5)
                ->get(),
        ];
        
        return Inertia::render('Dashboard/Index', [
            'stats' => $stats,
            'user' => $user,
        ]);
    }
}