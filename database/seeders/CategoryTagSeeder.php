<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Str;

class CategoryTagSeeder extends Seeder
{
    public function run()
    {
        // Seed Categories
        $categories = [
            ['name' => 'Electronics', 'description' => 'Electronic devices'],
            ['name' => 'Fashion', 'description' => 'Clothing and accessories'],
            ['name' => 'Home & Garden', 'description' => 'Home items'],
            ['name' => 'Books', 'description' => 'Books and stationery'],
            ['name' => 'Sports', 'description' => 'Sports equipment'],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'description' => $category['description'],
            ]);
        }

        // Seed Tags
        $tags = [
            'New Arrival', 'Best Seller', 'On Sale', 'Limited Edition',
            'Premium', 'Eco-Friendly', 'Handmade', 'Summer Collection',
        ];

        foreach ($tags as $tagName) {
            Tag::create([
                'name' => $tagName,
            ]);
        }

        $this->command->info('✅ Categories and Tags seeded!');
    }
}