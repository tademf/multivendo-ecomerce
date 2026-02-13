<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Carbon\Carbon;

class PromotionController extends Controller
{
    /**
     * Display a listing of promotions (Public) - ✅ COMPLETELY FIXED
     */
    public function index(Request $request)
    {
        try {
            // Get approved, active promotions
            $promotions = Promotion::with('user')
                ->where('status', Promotion::STATUS_APPROVED)
                ->where('published_at', '<=', now())
                ->where(function($q) {
                    $q->whereNull('expired_at')
                      ->orWhere('expired_at', '>=', now());
                })
                ->latest('published_at')
                ->paginate(12);
            
            // ✅ Accessors are automatically appended via $appends
            // No need to manually transform
            
            Log::info('Promotions fetched successfully', [
                'count' => $promotions->total(),
                'has_data' => $promotions->isNotEmpty()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Promotion index error: ' . $e->getMessage());
            $promotions = Promotion::with('user')
                ->where('status', Promotion::STATUS_APPROVED)
                ->paginate(12);
        }

        try {
            // Get featured promotions
            $featured = Promotion::with('user')
                ->where('status', Promotion::STATUS_APPROVED)
                ->where('published_at', '<=', now())
                ->where(function($q) {
                    $q->whereNull('expired_at')
                      ->orWhere('expired_at', '>=', now());
                })
                ->inRandomOrder()
                ->limit(6)
                ->get();
            
        } catch (\Exception $e) {
            Log::error('Featured promotions error: ' . $e->getMessage());
            $featured = [];
        }

        try {
            $categories = Category::select('id', 'name')->get();
        } catch (\Exception $e) {
            Log::error('Categories error: ' . $e->getMessage());
            $categories = [];
        }

        return Inertia::render('PromotionsPage', [
            'promotions' => $promotions,
            'featured' => $featured,
            'categories' => $categories,
            'filters' => $request->all(['search', 'sort']),
        ]);
    }

    /**
     * Display the specified promotion (Public)
     */
    public function show(Promotion $promotion)
    {
        if ($promotion->status !== Promotion::STATUS_APPROVED) {
            return redirect()->route('promotions.index')
                ->with('error', 'This promotion is not available.');
        }

        if ($promotion->expired_at && $promotion->expired_at < now()) {
            return redirect()->route('promotions.index')
                ->with('error', 'This promotion has expired.');
        }

        $promotion->load('user');

        $related = Promotion::with('user')
            ->where('status', Promotion::STATUS_APPROVED)
            ->where('id', '!=', $promotion->id)
            ->where('published_at', '<=', now())
            ->where(function($q) {
                $q->whereNull('expired_at')
                  ->orWhere('expired_at', '>=', now());
            })
            ->inRandomOrder()
            ->limit(3)
            ->get();

        return Inertia::render('PromotionDetailPage', [
            'promotion' => $promotion,
            'related' => $related,
        ]);
    }

    /**
     * Show create form (Vendor only)
     */
    public function create()
    {
        $categories = Category::all();
        return Inertia::render('Vendor/Promotions/Create', [
            'categories' => $categories
        ]);
    }

    /**
     * Edit promotion (Vendor only)
     */
    public function edit(Promotion $promotion)
    {
        if ($promotion->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $categories = Category::all();

        return Inertia::render('Vendor/Promotions/Edit', [
            'promotion' => $promotion,
            'categories' => $categories,
        ]);
    }

    /**
     * Update promotion (Vendor only)
     */
    public function update(Request $request, Promotion $promotion)
    {
        if ($promotion->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $rules = [
                'description' => 'required|string|max:1000',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
                'video' => 'nullable|file|mimes:mp4,mov,avi,mkv|max:512000',
            ];

            $request->validate($rules);

            $data = [
                'description' => $request->description,
                'status' => Promotion::STATUS_PENDING,
            ];

            // Handle video upload
            if ($request->hasFile('video')) {
                if ($promotion->video) {
                    Storage::disk('public')->delete($promotion->video);
                }
                $path = $request->file('video')->store('promotions/videos', 'public');
                $data['video'] = $path;
                Log::info('Video updated', ['path' => $path]);
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                if ($promotion->image) {
                    Storage::disk('public')->delete($promotion->image);
                }
                $path = $request->file('image')->store('promotions/images', 'public');
                $data['image'] = $path;
                Log::info('Image updated', ['path' => $path]);
            }

            // Update duration if provided
            if ($request->filled('duration')) {
                $days = (int)$request->duration;
                $publishDate = $promotion->published_at ?? now();
                $data['expired_at'] = Carbon::parse($publishDate)->addDays($days);
            }

            // Update published_at if provided
            if ($request->filled('published_at')) {
                $data['published_at'] = Carbon::parse($request->published_at);
            }

            $promotion->update($data);

            return redirect()->route('vendor.promotions.index')
                ->with('success', 'Promotion updated successfully. Pending re-approval.');

        } catch (\Exception $e) {
            Log::error('Promotion update failed', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Failed to update promotion: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * STORE - Local file upload only - ✅ FIXED
     */
    public function store(Request $request)
    {
        try {
            // Validate
            $rules = [
                'description' => 'required|string|max:1000',
                'duration' => 'required|string|in:1,7,14,21,30',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
                'video' => 'nullable|file|mimes:mp4,mov,avi,mkv|max:512000',
                'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            ];

            $validated = $request->validate($rules);

            $data = [
                'description' => $request->description,
                'user_id' => Auth::id(),
                'status' => Promotion::STATUS_PENDING,
                'published_at' => now(),
            ];

            // Handle video file upload
            if ($request->hasFile('video')) {
                $file = $request->file('video');
                $path = $file->store('promotions/videos', 'public');
                $data['video'] = $path;
                Log::info('Video uploaded', ['path' => $path, 'name' => $file->getClientOriginalName()]);
            }

            // Calculate expiry date
            $days = (int)$request->duration;
            $data['expired_at'] = Carbon::parse($data['published_at'])->addDays($days);

            // Handle image upload
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('promotions/images', 'public');
                $data['image'] = $path;
                Log::info('Image uploaded', ['path' => $path]);
            }

            // Handle payment proof upload
            if ($request->hasFile('payment_proof')) {
                $path = $request->file('payment_proof')->store('promotions/payments', 'public');
                $data['payment_proof'] = $path;
                Log::info('Payment proof uploaded', ['path' => $path]);
            }

            // Create promotion
            $promotion = Promotion::create($data);

            Log::info('Promotion created successfully', [
                'id' => $promotion->id,
                'user_id' => Auth::id(),
                'has_image' => !is_null($promotion->image),
                'has_video' => !is_null($promotion->video),
                'status' => $promotion->status
            ]);

            return redirect()->route('vendor.promotions.index')
                ->with('success', 'Promotion submitted successfully! Pending admin approval.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Promotion validation failed', ['errors' => $e->errors()]);
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Promotion creation failed', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Failed to create promotion: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Vendor promotions index
     */
    public function vendorIndex()
    {
        $promotions = Promotion::where('user_id', Auth::id())
            ->latest()
            ->paginate(15);

        return Inertia::render('Vendor/Promotions/Index', [
            'promotions' => $promotions
        ]);
    }

    /**
     * Get vendor's active promotions (API for Navbar) - ✅ FIXED
     */
    public function activePromotions()
    {
        if (!Auth::check()) {
            return response()->json([]);
        }

        try {
            $promotions = Promotion::where('user_id', Auth::id())
                ->where('status', Promotion::STATUS_APPROVED)
                ->where('published_at', '<=', now())
                ->where(function($q) {
                    $q->whereNull('expired_at')
                      ->orWhere('expired_at', '>=', now());
                })
                ->latest()
                ->get();

            return response()->json($promotions);

        } catch (\Exception $e) {
            Log::error('Active promotions fetch failed', ['error' => $e->getMessage()]);
            return response()->json([]);
        }
    }

    /**
     * Get vendor's pending promotions count (API for Navbar)
     */
    public function pendingCount()
    {
        if (!Auth::check()) {
            return response()->json(['count' => 0]);
        }

        try {
            $count = Promotion::where('user_id', Auth::id())
                ->where('status', Promotion::STATUS_PENDING)
                ->count();

            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            Log::error('Pending count fetch failed', ['error' => $e->getMessage()]);
            return response()->json(['count' => 0]);
        }
    }

    /**
     * Delete promotion (Vendor only)
     */
    public function destroy(Promotion $promotion)
    {
        if ($promotion->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Delete associated files
            if ($promotion->image) {
                Storage::disk('public')->delete($promotion->image);
            }
            if ($promotion->video) {
                Storage::disk('public')->delete($promotion->video);
            }
            if ($promotion->payment_proof) {
                Storage::disk('public')->delete($promotion->payment_proof);
            }

            $promotion->delete();

            Log::info('Promotion deleted successfully', ['id' => $promotion->id]);

            return redirect()->back()->with('success', 'Promotion deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Promotion deletion failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to delete promotion.');
        }
    }

    /**
     * Admin promotions index
     */
    public function adminIndex()
    {
        $promotions = Promotion::with('user')
            ->latest()
            ->paginate(15);

        return Inertia::render('Admin/Promotions/Index', [
            'promotions' => $promotions
        ]);
    }

    /**
     * Admin approve promotion
     */
    public function approve(Promotion $promotion)
    {
        try {
            $promotion->update([
                'status' => Promotion::STATUS_APPROVED,
                'published_at' => $promotion->published_at ?? now(),
            ]);

            Log::info('Promotion approved', ['id' => $promotion->id, 'admin_id' => Auth::id()]);

            return redirect()->back()->with('success', 'Promotion approved successfully.');

        } catch (\Exception $e) {
            Log::error('Promotion approval failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to approve promotion.');
        }
    }

    /**
     * Admin reject promotion
     */
    public function reject(Request $request, Promotion $promotion)
    {
        try {
            $request->validate(['reason' => 'required|string|max:500']);

            $promotion->update([
                'status' => Promotion::STATUS_REJECTED,
            ]);

            Log::info('Promotion rejected', ['id' => $promotion->id, 'admin_id' => Auth::id()]);

            return redirect()->back()->with('success', 'Promotion rejected successfully.');

        } catch (\Exception $e) {
            Log::error('Promotion rejection failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to reject promotion.');
        }
    }

    /**
     * Pending promotions (Admin only)
     */
    public function pending()
    {
        $promotions = Promotion::with('user')
            ->where('status', Promotion::STATUS_PENDING)
            ->latest()
            ->paginate(15);

        return Inertia::render('Admin/Promotions/Pending', [
            'promotions' => $promotions
        ]);
    }
}