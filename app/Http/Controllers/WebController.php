<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Wishlist;
use App\Models\Shipment;
use App\Models\StockHistory;

class WebController extends Controller
{
    /**
     * Check and create default categories/tags if they don't exist
     */
    private function ensureDefaultDataExists()
    {
        // Create default categories if none exist
        if (Category::count() === 0) {
            $defaultCategories = [
                ['name' => 'Electronics', 'description' => 'Electronic devices and gadgets'],
                ['name' => 'Fashion', 'description' => 'Clothing, shoes and accessories'],
                ['name' => 'Home & Kitchen', 'description' => 'Home appliances and kitchenware'],
                ['name' => 'Books', 'description' => 'Books and stationery'],
                ['name' => 'Sports', 'description' => 'Sports equipment and gear'],
            ];
            
            foreach ($defaultCategories as $category) {
                Category::create([
                    'name' => $category['name'],
                    'slug' => Str::slug($category['name']),
                    'description' => $category['description'],
                ]);
            }
        }

        // Create default tags if none exist
        if (Tag::count() === 0) {
            $defaultTags = [
                'New Arrival',
                'Best Seller', 
                'On Sale',
                'Limited Edition',
                'Premium Quality',
                'Eco-Friendly',
            ];
            
            foreach ($defaultTags as $tagName) {
                Tag::create([
                    'name' => $tagName,
                    'slug' => Str::slug($tagName),
                ]);
            }
        }
    }

    /**
     * Check if session is fresh (new browser session)
     */
    private function isFreshBrowserSession(Request $request)
    {
        // Check if this appears to be a new browser session
        $sessionId = $request->session()->getId();
        $previousSessionId = $request->cookie('laravel_session');
        
        // If no previous session cookie or session ID changed, it's fresh
        return !$previousSessionId || $previousSessionId !== $sessionId;
    }

    /**
     * Handle session expiration logic for each request
     */
    private function handleSessionExpiration(Request $request)
    {
        // Force session to expire on browser close for ALL requests
        config(['session.expire_on_close' => true]);
        
        // Check if this is a fresh browser session (no session cookie)
        if (!$request->hasSession() || !$request->session()->isStarted()) {
            // If user appears to be logged in but session is fresh, log them out
            if (Auth::check()) {
                // Check if they have a "remember me" cookie
                $hasRememberCookie = $request->hasCookie(Auth::getRecallerName());
                
                // If no remember cookie, they should be logged out
                if (!$hasRememberCookie) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                }
            }
        }
    }

    public function home(Request $request)
{
    $this->handleSessionExpiration($request);
    
    config(['session.expire_on_close' => true]);
    // Ensure default data exists
    $this->ensureDefaultDataExists();

    // Fetch real products from database WITH category and tags
    $products = Product::with(['category', 'tags'])
        ->orderBy('created_at', 'desc')
        ->take(20)
        ->get()
        ->map(function ($product) {
            return [
                'product_id' => $product->product_id,
                'name' => $product->name,
                'reference' => $product->reference,
                'description' => $product->description,
                'price' => floatval($product->price),
                'stock' => $product->stock,
                'category_id' => $product->category_id,
                'category' => $product->category->name ?? 'Uncategorized',
                'tags' => $product->tags->map(function ($tag) {
                    return [
                        'tag_id' => $tag->tag_id,
                        'name' => $tag->name,
                        'slug' => $tag->slug,
                    ];
                }),
                'image' => $product->image ? asset('storage/' . $product->image) : 'https://placehold.co/400x300/e0f2f1/065f46?text=Product+Image',
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ];
        });

    // Fetch popular tags
    $popularTags = Tag::withCount('products')
        ->orderBy('products_count', 'desc')
        ->take(10)
        ->get()
        ->map(function ($tag) {
            return [
                'tag_id' => $tag->tag_id,
                'name' => $tag->name,
                'slug' => $tag->slug,
                'product_count' => $tag->products_count,
            ];
        });

    // Get user wishlist IDs
    $wishlistIds = [];
    if (Auth::check()) {
        $wishlistIds = Wishlist::where('user_id', Auth::id())
            ->pluck('product_id')
            ->toArray();
    }

    return Inertia::render('HomePage', [
        'products' => $products,
        'popularTags' => $popularTags,
        'initialWishlistIds' => $wishlistIds,
        // categories and totalProducts are now globally available via Inertia middleware
    ]);
}

    public function login(Request $request)
    {
        $this->handleSessionExpiration($request);
        
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return Inertia::render('Auth/Login');
    }

    public function register(Request $request)
    {
        $this->handleSessionExpiration($request);
        
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return Inertia::render('Auth/Register');
    }

    public function products(Request $request)
    {
        $this->handleSessionExpiration($request);
        
        // Ensure default data exists
        $this->ensureDefaultDataExists();

        // Fetch all products for public products page WITH tags
        // REMOVED: ->where('stock', '>', 0) // This was hiding products with 0 stock
        $products = Product::with(['category', 'tags'])
            ->orderBy('created_at', 'desc')
            ->paginate(12)
            ->through(function ($product) {
                return [
                    'product_id' => $product->product_id,
                    'name' => $product->name,
                    'reference' => $product->reference,
                    'description' => $product->description,
                    'price' => floatval($product->price),
                    'stock' => $product->stock,
                    'category_id' => $product->category_id,
                    'category' => $product->category->name ?? 'Uncategorized',
                    'tags' => $product->tags->map(function ($tag) {
                        return [
                            'tag_id' => $tag->tag_id,
                            'name' => $tag->name,
                            'slug' => $tag->slug,
                        ];
                    }),
                    'image' => $product->image ? asset('storage/' . $product->image) : 'https://placehold.co/400x300/e0f2f1/065f46?text=Product+Image',
                    'created_at' => $product->created_at,
                ];
            });

        $categories = Category::all()->map(function ($category) {
            return [
                'category_id' => $category->category_id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
            ];
        });

        // Get all tags for filter checkboxes
        $allTags = Tag::withCount('products')
            ->orderBy('name')
            ->get()
            ->map(function ($tag) {
                return [
                    'tag_id' => $tag->tag_id,
                    'name' => $tag->name,
                    'slug' => $tag->slug,
                    'product_count' => $tag->products_count,
                ];
            });

        // Get user wishlist IDs
        $wishlistIds = [];
        if (Auth::check()) {
            $wishlistIds = Wishlist::where('user_id', Auth::id())
                ->pluck('product_id')
                ->toArray();
        }

        return Inertia::render('ProductsPage', [
            'products' => $products,
            'categories' => $categories,
            'allTags' => $allTags,
            'initialWishlistIds' => $wishlistIds,
            'auth' => [ // ADD THIS for user authentication in Vue
                'user' => Auth::user()
            ],
        ]);
    }

    public function productDetail(Request $request, $id)
    {
        $this->handleSessionExpiration($request);
        
        // Ensure default data exists
        $this->ensureDefaultDataExists();

        // Fetch real product by ID from database WITH tags
        $product = Product::with(['category', 'user', 'tags'])->find($id);
        
        if (!$product) {
            abort(404);
        }

        $productData = [
            'product_id' => $product->product_id,
            'name' => $product->name,
            'reference' => $product->reference,
            'description' => $product->description,
            'price' => floatval($product->price),
            'stock' => $product->stock,
            'category_id' => $product->category_id,
            'category' => $product->category->name ?? 'Uncategorized',
            'seller' => $product->user->full_name ?? $product->user->email ?? 'Unknown',
            'tags' => $product->tags->map(function ($tag) {
                return [
                    'tag_id' => $tag->tag_id,
                    'name' => $tag->name,
                    'slug' => $tag->slug,
                ];
            }),
            'image' => $product->image ? asset('storage/' . $product->image) : 'https://placehold.co/400x300/e0f2f1/065f46?text=Product+Image',
            'created_at' => $product->created_at->format('M d, Y'),
        ];

        // Get related products (same category) WITH tags
        // REMOVED: ->where('stock', '>', 0) // This was hiding products with 0 stock
        $relatedProducts = Product::with(['category', 'tags'])
            ->where('category_id', $product->category_id)
            ->where('product_id', '!=', $id)
            ->take(4)
            ->get()
            ->map(function ($related) {
                return [
                    'product_id' => $related->product_id,
                    'name' => $related->name,
                    'price' => floatval($related->price),
                    'image' => $related->image ? asset('storage/' . $related->image) : 'https://placehold.co/400x300/e0f2f1/065f46?text=Product+Image',
                    'category' => $related->category->name ?? 'Uncategorized',
                    'tags' => $related->tags->map(function ($tag) {
                        return [
                            'tag_id' => $tag->tag_id,
                            'name' => $tag->name,
                        ];
                    }),
                ];
            });

        // Get real wishlist status
        $isInWishlist = false;
        if (Auth::check()) {
            $isInWishlist = Wishlist::where('user_id', Auth::id())
                ->where('product_id', $id)
                ->exists();
        }

        // Get all tags for edit form
        $allTags = Tag::orderBy('name')
            ->get()
            ->map(function ($tag) {
                return [
                    'tag_id' => $tag->tag_id,
                    'name' => $tag->name,
                ];
            });

        return Inertia::render('ProductDetailPage', [
            'product' => $productData,
            'relatedProducts' => $relatedProducts,
            'isInWishlist' => $isInWishlist,
            'allTags' => $allTags,
            'auth' => [ // ADD THIS for user authentication in Vue
                'user' => Auth::user()
            ],
        ]);
    }

    public function cart(Request $request)
    {
        $this->handleSessionExpiration($request);
        
        $cartItems = [];
        
        return Inertia::render('CartPage', [
            'cartItems' => $cartItems,
            'auth' => [ // ADD THIS for user authentication in Vue
                'user' => Auth::user()
            ],
        ]);
    }

    public function orders(Request $request)
    {
        $this->handleSessionExpiration($request);
        
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        // Changed to use Shipments instead of Orders
        $shipments = Shipment::with(['items.product'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($shipment) {
                return [
                    'id' => $shipment->id,
                    'delivery_address' => $shipment->delivery_address,
                    'status' => $shipment->status,
                    'status_text' => $shipment->getStatusTextAttribute(),
                    'total_amount' => $shipment->total_amount,
                    'formatted_total' => $shipment->getFormattedTotalAttribute(),
                    'shipping_cost' => $shipment->shipping_cost,
                    'tracking_number' => $shipment->tracking_number,
                    'carrier' => $shipment->carrier,
                    'created_at' => $shipment->created_at->format('M d, Y'),
                    'shipped_at' => $shipment->shipped_at?->format('M d, Y'),
                    'delivered_at' => $shipment->delivered_at?->format('M d, Y'),
                    'items_count' => $shipment->items->count(),
                    'items' => $shipment->items->map(function ($item) {
                        return [
                            'product_name' => $item->product_name,
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                            'total_price' => $item->total_price,
                        ];
                    }),
                ];
            });
        
        return Inertia::render('OrdersPage', [
            'shipments' => $shipments,
            'stats' => [
                'total' => Shipment::where('user_id', Auth::id())->count(),
                'pending' => Shipment::where('user_id', Auth::id())->where('status', 'pending')->count(),
                'shipped' => Shipment::where('user_id', Auth::id())->where('status', 'shipped')->count(),
                'delivered' => Shipment::where('user_id', Auth::id())->where('status', 'delivered')->count(),
                'cancelled' => Shipment::where('user_id', Auth::id())->where('status', 'cancelled')->count(),
            ],
            'auth' => [ // ADD THIS for user authentication in Vue
                'user' => Auth::user()
            ],
        ]);
    }

    public function wishlist(Request $request)
    {
        $this->handleSessionExpiration($request);
        
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        // Get real wishlist items WITH tags
        $wishlistItems = Wishlist::where('user_id', Auth::id())
            ->with('product.category', 'product.tags')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($wishlist) {
                $product = $wishlist->product;
                return [
                    'product_id' => $product->product_id,
                    'name' => $product->name,
                    'reference' => $product->reference,
                    'description' => $product->description,
                    'price' => floatval($product->price),
                    'stock' => $product->stock,
                    'category' => $product->category->name ?? 'Uncategorized',
                    'tags' => $product->tags->map(function ($tag) {
                        return [
                            'tag_id' => $tag->tag_id,
                            'name' => $tag->name,
                        ];
                    }),
                    'image' => $product->image ? asset('storage/' . $product->image) : 'https://placehold.co/400x300/e0f2f1/065f46?text=Product+Image',
                    'added_at' => $wishlist->created_at->format('M d, Y'),
                ];
            });
        
        return Inertia::render('WishlistPage', [
            'wishlistItems' => $wishlistItems,
            'wishlistCount' => $wishlistItems->count(),
            'auth' => [ // ADD THIS for user authentication in Vue
                'user' => Auth::user()
            ],
        ]);
    }

    public function profile(Request $request)
    {
        $this->handleSessionExpiration($request);
        
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        return Inertia::render('Profile/Index', [
            'user' => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'national_id' => $user->national_id,
                'profile_picture' => $user->profile_picture ? asset('storage/' . $user->profile_picture) : null,
                'verified_at' => $user->verified_at,
                'created_at' => $user->created_at->format('M d, Y'),
            ],
            'auth' => [ // ADD THIS for user authentication in Vue
                'user' => Auth::user()
            ],
        ]);
    }

    public function dashboard(Request $request)
    {
        $this->handleSessionExpiration($request);
        
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        // Ensure default data exists
        $this->ensureDefaultDataExists();
        
        $user = Auth::user();
        
        // Show user's products WITH tags
        $userProducts = Product::where('user_id', $user->id)
            ->with(['category', 'tags'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($product) {
                return [
                    'product_id' => $product->product_id,
                    'name' => $product->name,
                    'price' => floatval($product->price),
                    'stock' => $product->stock,
                    'category' => $product->category->name ?? 'Uncategorized',
                    'tags' => $product->tags->map(function ($tag) {
                        return [
                            'tag_id' => $tag->tag_id,
                            'name' => $tag->name,
                        ];
                    }),
                    'image' => $product->image ? asset('storage/' . $product->image) : null,
                    'created_at' => $product->created_at->format('M d, Y'),
                    'needs_restock' => $product->needs_restock,
                ];
            });
        
        // Get recent stock history
        $recentStockHistory = StockHistory::whereHas('product', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($history) {
                return [
                    'id' => $history->id,
                    'product_name' => $history->product->name,
                    'type' => $history->type,
                    'type_name' => $history->getTypeNameAttribute(),
                    'quantity' => $history->quantity,
                    'quantity_with_sign' => $history->getQuantityWithSignAttribute(),
                    'stock_before' => $history->stock_before,
                    'stock_after' => $history->stock_after,
                    'created_at' => $history->created_at->format('M d, Y H:i'),
                ];
            });
        
        $stats = [
            'total_products' => Product::where('user_id', $user->id)->count(),
            'products_need_restock' => Product::where('user_id', $user->id)->where('needs_restock', true)->count(),
            'total_shipments' => Shipment::where('user_id', $user->id)->count(),
            'pending_shipments' => Shipment::where('user_id', $user->id)->where('status', 'pending')->count(),
            'total_wishlist' => Wishlist::where('user_id', $user->id)->count(),
            'total_sales' => StockHistory::whereHas('product', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('type', 'sale')->sum('quantity'),
        ];
        
        return Inertia::render('Dashboard/Index', [
            'stats' => $stats,
            'userProducts' => $userProducts,
            'recentStockHistory' => $recentStockHistory,
            'auth' => [ // ADD THIS for user authentication in Vue
                'user' => Auth::user()
            ],
        ]);
    }

    public function messages(Request $request)
    {
        $this->handleSessionExpiration($request);
        
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        return Inertia::render('Messages/Index', [
            'messages' => [],
            'auth' => [ // ADD THIS for user authentication in Vue
                'user' => Auth::user()
            ],
        ]);
    }

    public function notifications(Request $request)
    {
        $this->handleSessionExpiration($request);
        
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        return Inertia::render('Notifications/Index', [
            'notifications' => [],
            'auth' => [ // ADD THIS for user authentication in Vue
                'user' => Auth::user()
            ],
        ]);
    }

    public function settings(Request $request)
    {
        $this->handleSessionExpiration($request);
        
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        return Inertia::render('Settings/Index', [
            'user' => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'created_at' => $user->created_at->format('M d, Y'),
            ],
            'auth' => [ // ADD THIS for user authentication in Vue
                'user' => Auth::user()
            ],
        ]);
    }

    /**
     * Helper method to manually add test categories/tags
     * Visit /add-test-data to trigger
     */
    public function addTestData(Request $request)
    {
        $this->handleSessionExpiration($request);
        $this->ensureDefaultDataExists();
        
        return redirect()->back()->with('success', 'Test categories and tags added successfully!');
    }
    
    /**
     * Check session status (for debugging)
     */
    public function checkSession(Request $request)
    {
        return response()->json([
            'logged_in' => Auth::check(),
            'session_id' => $request->session()->getId(),
            'session_started' => $request->session()->isStarted(),
            'has_remember_cookie' => $request->hasCookie(Auth::getRecallerName()),
            'config_expire_on_close' => config('session.expire_on_close'),
        ]);
    }
}