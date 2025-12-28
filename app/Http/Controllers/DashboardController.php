<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userId = $user->id;
        
        // Count shipments for products that belong to the current user (vendor)
        // First get all product IDs that belong to this user
        $productIds = Product::where('user_id', $userId)->pluck('product_id');
        
        // Count shipments for those products
        $totalOrders = 0;
        if ($productIds->count() > 0) {
            $totalOrders = Shipment::whereIn('product_id', $productIds)->count();
        }
        
        $stats = [
            'total_products' => Product::where('user_id', $userId)->count(),
            'total_orders' => $totalOrders,
            'total_wishlist' => Wishlist::where('user_id', $userId)->count(),
            'recent_products' => Product::where('user_id', $userId)
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