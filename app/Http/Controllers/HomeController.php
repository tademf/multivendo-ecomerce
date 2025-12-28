<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Wishlist;
use App\Models\Discount;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    // UPDATED: Accept search and category parameters from request
    public function index(Request $request)
    {
        // Get search and category from URL parameters
        $searchTerm = $request->input('search', '');
        $categoryName = $request->input('category', '');
        
        // Base query for products WITH DISCOUNT
        $query = Product::with(['category', 'discount'])
            ->where('stock', '>', 0);
        
        // Apply search filter if search term exists
        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('reference', 'like', "%{$searchTerm}%");
            });
        }
        
        // Apply category filter if category exists
        if ($categoryName) {
            $category = Category::where('name', $categoryName)->first();
            if ($category) {
                $query->where('category_id', $category->category_id);
            }
        }
        
        // Get products with filters applied
        $products = $query->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($product) {
                $productData = [
                    'product_id' => $product->product_id,
                    'name' => $product->name,
                    'price' => floatval($product->price),
                    'image' => $product->image ? asset('storage/' . $product->image) : 'https://placehold.co/400x300/e0f2f1/065f46?text=Product',
                    'category_id' => $product->category_id,
                    'stock' => $product->stock,
                    'description' => $product->description,
                    'reference' => $product->reference,
                    'created_at' => $product->created_at,
                    'user_id' => $product->user_id,
                    'tags' => $product->tags ?? [],
                ];
                
                // Add discount data if exists
                if ($product->discount && $product->discount->status === 'active') {
                    $discountAmount = floatval($product->discount->discount_amount);
                    $originalPrice = floatval($product->price);
                    $discountedPrice = $originalPrice - ($originalPrice * $discountAmount / 100);
                    
                    $productData['discount'] = [
                        'discount_id' => $product->discount->discount_id,
                        'discount_name' => $product->discount->discount_name,
                        'discount_amount' => $discountAmount,
                        'status' => $product->discount->status,
                        'start_date' => $product->discount->start_date,
                        'end_date' => $product->discount->end_date,
                        'discounted_price' => $discountedPrice,
                    ];
                }
                
                return $productData;
            });
        
        // Get all categories
        $categories = Category::all()->map(function ($category) {
            return [
                'category_id' => $category->category_id,
                'name' => $category->name,
                'slug' => $category->slug ?? strtolower(str_replace(' ', '-', $category->name)),
                'description' => $category->description,
                'product_count' => $category->products()->where('stock', '>', 0)->count(),
            ];
        });
        
        // Get wishlist IDs for authenticated users
        // $wishlistIds = [];
        // if (Auth::check()) {
        //     $wishlistIds = Wishlist::where('user_id', Auth::id())
        //         ->pluck('product_id')
        //         ->toArray();
        // }
        
        // Get featured products WITH DISCOUNT
        $featuredProducts = Product::with(['category', 'discount'])
            ->where('stock', '>', 0)
            ->limit(8)
            ->get()
            ->map(function ($product) {
                $productData = [
                    'product_id' => $product->product_id,
                    'name' => $product->name,
                    'price' => floatval($product->price),
                    'image' => $product->image ? asset('storage/' . $product->image) : 'https://placehold.co/400x300/e0f2f1/065f46?text=Product',
                    'stock' => $product->stock,
                ];
                
                // Add discount data if exists
                if ($product->discount && $product->discount->status === 'active') {
                    $discountAmount = floatval($product->discount->discount_amount);
                    $originalPrice = floatval($product->price);
                    $discountedPrice = $originalPrice - ($originalPrice * $discountAmount / 100);
                    
                    $productData['discount'] = [
                        'discount_id' => $product->discount->discount_id,
                        'discount_name' => $product->discount->discount_name,
                        'discount_amount' => $discountAmount,
                        'status' => $product->discount->status,
                        'discounted_price' => $discountedPrice,
                    ];
                }
                
                return $productData;
            });
        
        // Get best sellers WITH DISCOUNT
        $bestSellers = Product::with(['category', 'discount'])
            ->where('stock', '>', 0)
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get()
            ->map(function ($product) {
                $productData = [
                    'product_id' => $product->product_id,
                    'name' => $product->name,
                    'price' => floatval($product->price),
                    'image' => $product->image ? asset('storage/' . $product->image) : 'https://placehold.co/400x300/e0f2f1/065f46?text=Product',
                    'stock' => $product->stock,
                ];
                
                // Add discount data if exists
                if ($product->discount && $product->discount->status === 'active') {
                    $discountAmount = floatval($product->discount->discount_amount);
                    $originalPrice = floatval($product->price);
                    $discountedPrice = $originalPrice - ($originalPrice * $discountAmount / 100);
                    
                    $productData['discount'] = [
                        'discount_id' => $product->discount->discount_id,
                        'discount_name' => $product->discount->discount_name,
                        'discount_amount' => $discountAmount,
                        'status' => $product->discount->status,
                        'discounted_price' => $discountedPrice,
                    ];
                }
                
                return $productData;
            });
        
        // Calculate discount stats
        $activeDiscounts = Discount::where('status', 'active')
            ->where('end_date', '>', now())
            ->count();
            
        $discountedProducts = Product::whereHas('discount', function($query) {
                $query->where('status', 'active')
                      ->where('end_date', '>', now());
            })
            ->where('stock', '>', 0)
            ->count();
        
        return Inertia::render('HomePage', [
            'products' => $products,
            'categories' => $categories,
            'featuredProducts' => $featuredProducts,
            'bestSellers' => $bestSellers,
            // 'initialWishlistIds' => $wishlistIds,
            // PASS SEARCH AND CATEGORY TO THE FRONTEND
            'search' => $searchTerm,
            'category' => $categoryName,
            'stats' => [
                'total_products' => Product::count(),
                'in_stock' => Product::where('stock', '>', 0)->count(),
                'total_categories' => Category::count(),
                'discounted_products' => $discountedProducts, // ADDED: Count of products with active discounts
                'active_discounts' => $activeDiscounts, // ADDED: Count of active discounts
                'on_sale' => Product::where('stock', '<', 10)->count(), // Low stock items
            ],
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ]
        ]);
    }
    
    public function search(Request $request)
    {
        $query = Product::with(['category', 'discount'])
            ->where('stock', '>', 0);
        
        if ($request->has('q') && $request->q) {
            $searchTerm = $request->q;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('reference', 'like', "%{$searchTerm}%");
            });
        }
        
        if ($request->has('category') && $request->category) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->category);
            });
        }
        
        $products = $query->paginate(12);
        
        return Inertia::render('SearchPage', [
            'products' => $products,
            'searchTerm' => $request->q ?? '',
            'category' => $request->category ?? '',
        ]);
    }
    
    public function categoryProducts($categorySlug)
    {
        $category = Category::where('slug', $categorySlug)->firstOrFail();
        
        $products = Product::with(['category', 'discount'])
            ->where('category_id', $category->category_id)
            ->where('stock', '>', 0)
            ->paginate(12);
        
        return Inertia::render('CategoryPage', [
            'category' => $category,
            'products' => $products,
        ]);
    }
    
    // NEW METHOD: Get discounted products
    public function discountedProducts()
    {
        $products = Product::with(['category', 'discount'])
            ->whereHas('discount', function($query) {
                $query->where('status', 'active')
                      ->where('end_date', '>', now());
            })
            ->where('stock', '>', 0)
            ->paginate(12);
        
        return Inertia::render('DiscountedPage', [
            'products' => $products,
            'title' => 'Discounted Products',
            'description' => 'Special offers and discounts available now!',
        ]);
    }
}