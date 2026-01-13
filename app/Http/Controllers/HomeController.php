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
                    
                    // Add discount data directly to product array (not nested in 'discount')
                    $productData['discount_percent'] = $discountAmount;
                    $productData['discount_name'] = $product->discount->discount_name;
                    $productData['discounted_price'] = $discountedPrice;
                    $productData['discount_status'] = $product->discount->status;
                    $productData['discount_start_date'] = $product->discount->start_date;
                    $productData['discount_end_date'] = $product->discount->end_date;
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
        
        // Get discounted products for the dedicated section
        $discountedProducts = Product::with(['category', 'discount'])
            ->whereHas('discount', function($query) {
                $query->where('status', 'active')
                      ->where('end_date', '>', now());
            })
            ->where('stock', '>', 0)
            ->limit(12)
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
                ];
                
                if ($product->discount && $product->discount->status === 'active') {
                    $discountAmount = floatval($product->discount->discount_amount);
                    $originalPrice = floatval($product->price);
                    $discountedPrice = $originalPrice - ($originalPrice * $discountAmount / 100);
                    
                    // Add discount data directly to product array
                    $productData['discount_percent'] = $discountAmount;
                    $productData['discount_name'] = $product->discount->discount_name;
                    $productData['discounted_price'] = $discountedPrice;
                }
                
                return $productData;
            });
        
        return Inertia::render('HomePage', [
            'products' => $products,
            'categories' => $categories,
            // PASS DISCOUNTED PRODUCTS SEPARATELY
            'discounted_products' => $discountedProducts,
            // PASS SEARCH AND CATEGORY TO THE FRONTEND
            'search' => $searchTerm,
            'category' => $categoryName,
            'stats' => [
                'total_products' => Product::count(),
                'in_stock' => Product::where('stock', '>', 0)->count(),
                'total_categories' => Category::count(),
                'discounted_products' => $discountedProducts->count(),
                'active_discounts' => Discount::where('status', 'active')
                    ->where('end_date', '>', now())
                    ->count(),
            ],
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ]
        ]);
    }
    
    // Add this new method for recently viewed API
    public function getRecentlyViewed(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([]);
        }
        
        // Get recently viewed from session or database
        $recentlyViewed = session()->get('recently_viewed', []);
        
        if (empty($recentlyViewed)) {
            return response()->json([]);
        }
        
        // Get products from the recently viewed IDs
        $productIds = array_slice($recentlyViewed, 0, 10); // Limit to 10
        
        $products = Product::with(['category'])
            ->whereIn('product_id', $productIds)
            ->where('stock', '>', 0)
            ->get()
            ->map(function ($product) use ($recentlyViewed) {
                // Find the timestamp for this product
                $viewedAt = null;
                foreach ($recentlyViewed as $item) {
                    if ($item['product_id'] == $product->product_id) {
                        $viewedAt = $item['viewed_at'] ?? now();
                        break;
                    }
                }
                
                return [
                    'product_id' => $product->product_id,
                    'name' => $product->name,
                    'price' => floatval($product->price),
                    'image' => $product->image ? asset('storage/' . $product->image) : 'https://placehold.co/400x300/e0f2f1/065f46?text=Product',
                    'category_id' => $product->category_id,
                    'stock' => $product->stock,
                    'viewed_at' => $viewedAt,
                ];
            });
        
        return response()->json($products);
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