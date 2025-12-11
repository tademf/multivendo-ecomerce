<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Product;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view products
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Product $product): bool
    {
        return $user->id === $product->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // All authenticated users can create products
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Product $product): bool|Response
    {
        if ($user->id === $product->user_id) {
            return true;
        }
        
        return Response::deny('You do not own this product.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Product $product): bool|Response
    {
        if ($user->id === $product->user_id) {
            return true;
        }
        
        return Response::deny('You do not own this product.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Product $product): bool
    {
        return $user->id === $product->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Product $product): bool
    {
        return $user->id === $product->user_id;
    }

    /**
     * Determine whether the user can manage stock for the product.
     */
    public function manageStock(User $user, Product $product): bool
    {
        return $user->id === $product->user_id;
    }

    /**
     * Determine whether the user can update product image.
     */
    public function updateImage(User $user, Product $product): bool
    {
        return $user->id === $product->user_id;
    }

    /**
     * Determine whether the user can update product tags.
     */
    public function updateTags(User $user, Product $product): bool
    {
        return $user->id === $product->user_id;
    }

    /**
     * Determine whether the user can update product pricing.
     */
    public function updatePricing(User $user, Product $product): bool
    {
        return $user->id === $product->user_id;
    }

    /**
     * Determine whether the user can export products.
     */
    public function export(User $user): bool
    {
        return true; // All authenticated users can export their products
    }

    /**
     * Determine whether the user can view product analytics.
     */
    public function viewAnalytics(User $user, Product $product): bool
    {
        return $user->id === $product->user_id;
    }
}