<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

class DiscountController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;
        
        // Start query with filters
        $query = Discount::where('user_id', $userId)
            ->with('product')
            ->latest();

        // Apply status filter
        if ($request->filled('status') && in_array($request->status, ['active', 'upcoming', 'expired'])) {
            $query->where('status', $request->status);
        }

        $discounts = $query->get();
        $products = Product::where('user_id', $userId)->get();

        // Calculate stats
        $total = $discounts->count();
        $active = $discounts->where('status', 'active')->count();
        $upcoming = $discounts->where('status', 'upcoming')->count();
        $expired = $discounts->where('status', 'expired')->count();

        return Inertia::render('DiscountsPage', [ // FIXED: Changed from 'Discounts/DiscountsPage'
            'discounts' => $discounts,
            'products' => $products,
            'user' => $user,
            'filterStatus' => $request->status ?? '',
            'discountStats' => [
                'total' => $total,
                'active' => $active,
                'upcoming' => $upcoming,
                'expired' => $expired,
            ],
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'discount_name' => 'required|string|max:255',
            'product_id' => 'required|exists:products,product_id',
            'discount_amount' => 'required|numeric|min:1|max:100',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ]);

        // Check for overlapping discounts
        $overlap = Discount::where('product_id', $validated['product_id'])
            ->where('user_id', $user->id)
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                    ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('start_date', '<=', $validated['start_date'])
                            ->where('end_date', '>=', $validated['end_date']);
                    });
            })
            ->where('status', '!=', 'expired')
            ->exists();

        if ($overlap) {
            return redirect()->back()
                ->with('error', 'A discount already exists for this product during the selected period.');
        }

        // Calculate status
        $today = Carbon::today();
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);

        if ($today->between($startDate, $endDate)) {
            $status = 'active';
        } elseif ($today->greaterThan($endDate)) {
            $status = 'expired';
        } else {
            $status = 'upcoming';
        }

        Discount::create([
            'discount_name' => $validated['discount_name'],
            'product_id' => $validated['product_id'],
            'discount_amount' => $validated['discount_amount'],
            'user_id' => $user->id,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => $status,
        ]);

        return redirect()->route('discounts.index')
            ->with('success', 'Discount created successfully!');
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $discount = Discount::findOrFail($id);

        // Authorization check
        if ($discount->user_id != $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'discount_name' => 'required|string|max:255',
            'product_id' => 'required|exists:products,product_id',
            'discount_amount' => 'required|numeric|min:1|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        // Check for overlapping discounts (excluding current)
        $overlap = Discount::where('product_id', $validated['product_id'])
            ->where('user_id', $user->id)
            ->where('discount_id', '!=', $id)
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                    ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('start_date', '<=', $validated['start_date'])
                            ->where('end_date', '>=', $validated['end_date']);
                    });
            })
            ->where('status', '!=', 'expired')
            ->exists();

        if ($overlap) {
            return redirect()->back()
                ->with('error', 'A discount already exists for this product during the selected period.');
        }

        // Calculate status
        $today = Carbon::today();
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);

        if ($today->between($startDate, $endDate)) {
            $status = 'active';
        } elseif ($today->greaterThan($endDate)) {
            $status = 'expired';
        } else {
            $status = 'upcoming';
        }

        $discount->update([
            'discount_name' => $validated['discount_name'],
            'product_id' => $validated['product_id'],
            'discount_amount' => $validated['discount_amount'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => $status,
        ]);

        return redirect()->route('discounts.index')
            ->with('success', 'Discount updated successfully!');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $discount = Discount::findOrFail($id);

        // Authorization check
        if ($discount->user_id != $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $discount->delete();

        return redirect()->route('discounts.index')
            ->with('success', 'Discount deleted successfully!');
    }
}