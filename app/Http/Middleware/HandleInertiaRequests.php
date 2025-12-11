<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Category;
use App\Models\Product;
use App\Models\Wishlist;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->full_name,
                    'full_name' => $request->user()->full_name,
                    'email' => $request->user()->email,
                    'phone' => $request->user()->phone,
                    'national_id' => $request->user()->national_id,
                    'profile_picture' => $request->user()->profile_picture 
                        ? (str_starts_with($request->user()->profile_picture, 'http') 
                            ? $request->user()->profile_picture 
                            : asset('storage/' . $request->user()->profile_picture))
                        : null,
                    'verified_at' => $request->user()->verified_at,
                    'email_verified_at' => $request->user()->email_verified_at,
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'warning' => fn () => $request->session()->get('warning'),
                'info' => fn () => $request->session()->get('info'),
            ],
            // Categories available globally for all pages (navbar, filters, etc.)
            'categories' => fn () => Cache::remember('categories_with_count', 3600, function () {
                return Category::withCount('products')
                    ->orderBy('name')
                    ->get()
                    ->map(function ($category) {
                        return [
                            'category_id' => $category->category_id,
                            'name' => $category->name,
                            'slug' => $category->slug,
                            'description' => $category->description,
                            'product_count' => $category->products_count,
                        ];
                    });
            }),
            
            // Total products count for "All Categories"
            'totalProducts' => fn () => Cache::remember('total_products_count', 3600, function () {
                return Product::count();
            }),
            
            // Cart and wishlist counts
            'cart_count' => function () use ($request) {
                if ($request->user()) {
                    // You can implement cart count logic here
                    // For example, from session or database
                    return session()->get('cart_count', 0);
                }
                return session()->get('cart_count', 0);
            },
            
            'wishlist_count' => function () use ($request) {
                if ($request->user()) {
                    return Wishlist::where('user_id', $request->user()->id)->count();
                }
                return 0;
            },
            
            // Optional: Recent products for suggestions
            'recent_products' => fn () => Cache::remember('recent_products', 1800, function () {
                return Product::with('category')
                    ->orderBy('created_at', 'desc')
                    ->take(8)
                    ->get()
                    ->map(function ($product) {
                        return [
                            'product_id' => $product->product_id,
                            'name' => $product->name,
                            'price' => floatval($product->price),
                            'image' => $product->image ? asset('storage/' . $product->image) : 'https://placehold.co/400x300/e0f2f1/065f46?text=Product+Image',
                            'category_name' => $product->category->name ?? 'Uncategorized',
                        ];
                    });
            }),
            
            // Optional: Featured products
            'featured_products' => fn () => Cache::remember('featured_products', 1800, function () {
                return Product::with('category')
                    ->where('is_featured', true)
                    ->orderBy('created_at', 'desc')
                    ->take(6)
                    ->get()
                    ->map(function ($product) {
                        return [
                            'product_id' => $product->product_id,
                            'name' => $product->name,
                            'price' => floatval($product->price),
                            'image' => $product->image ? asset('storage/' . $product->image) : 'https://placehold.co/400x300/e0f2f1/065f46?text=Product+Image',
                            'category_name' => $product->category->name ?? 'Uncategorized',
                        ];
                    });
            }),
        ]);
    }
}