<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Product;
use App\Policies\ProductPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Product::class => ProductPolicy::class,
        // Only Product policy remains
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define global gates that apply to all users
        Gate::before(function ($user, $ability) {
            // Super admin has all permissions
            if (isset($user->role) && $user->role === 'super-admin') {
                return true;
            }
        });

        // Define custom gates
        Gate::define('manage-products', function ($user) {
            return isset($user->role) && ($user->role === 'admin' || $user->role === 'vendor');
        });

        Gate::define('view-dashboard', function ($user) {
            return isset($user->role) && in_array($user->role, ['admin', 'vendor', 'manager']);
        });

        Gate::define('manage-users', function ($user) {
            return isset($user->role) && $user->role === 'admin';
        });

        Gate::define('manage-categories', function ($user) {
            return isset($user->role) && ($user->role === 'admin' || $user->role === 'vendor');
        });

        Gate::define('export-data', function ($user) {
            return isset($user->role) && in_array($user->role, ['admin', 'vendor', 'manager']);
        });

        Gate::define('view-reports', function ($user) {
            return isset($user->role) && in_array($user->role, ['admin', 'manager']);
        });

        // Permission based on ownership
        Gate::define('view-product-analytics', function ($user, Product $product) {
            return $user->id === $product->user_id || (isset($user->role) && $user->role === 'admin');
        });

        // Define gate for admin only actions
        Gate::define('admin-only', function ($user) {
            return isset($user->role) && $user->role === 'admin';
        });

        // Define gate for vendor actions
        Gate::define('vendor-only', function ($user) {
            return isset($user->role) && $user->role === 'vendor';
        });

        // Define gate for customer actions
        Gate::define('customer-only', function ($user) {
            return isset($user->role) && $user->role === 'customer';
        });
    }
}