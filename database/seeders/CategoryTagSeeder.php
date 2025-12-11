<?php

// namespace Database\Seeders;

// use Illuminate\Database\Seeder;
// use App\Models\Category;
// use App\Models\Tag;

// class CategoryTagSeeder extends Seeder
// {
//     public function run()
//     {
        // Categories with descriptions
//         $categories = [
//             [
//                 'name' => 'Electronics', 
//                 'description' => 'Electronic devices and gadgets'
//             ],
//             [
//                 'name' => 'Clothing', 
//                 'description' => 'Fashion and apparel items'
//             ],
//             [
//                 'name' => 'Books', 
//                 'description' => 'Books and reading materials'
//             ],
//             [
//                 'name' => 'Home & Garden', 
//                 'description' => 'Home improvement and gardening products'
//             ],
//             [
//                 'name' => 'Sports', 
//                 'description' => 'Sports equipment and accessories'
//             ],
//             [
//                 'name' => 'Toys', 
//                 'description' => 'Toys and games for all ages'
//             ],
//             [
//                 'name' => 'Food & Beverages', 
//                 'description' => 'Food items and drinks'
//             ],
//             [
//                 'name' => 'Health & Beauty', 
//                 'description' => 'Health and beauty products'
//             ],
//         ];

//         foreach ($categories as $category) {
//             Category::create($category);
//         }

//         // Tags with descriptions
//         $tags = [
//             [
//                 'name' => 'New Arrival', 
//                 'description' => 'Recently added products'
//             ],
//             [
//                 'name' => 'Best Seller', 
//                 'description' => 'Top selling products'
//             ],
//             [
//                 'name' => 'On Sale', 
//                 'description' => 'Products with discounts'
//             ],
//             [
//                 'name' => 'Limited Edition', 
//                 'description' => 'Limited quantity available'
//             ],
//             [
//                 'name' => 'Eco Friendly', 
//                 'description' => 'Environmentally friendly products'
//             ],
//             [
//                 'name' => 'Premium', 
//                 'description' => 'High quality premium products'
//             ],
//             [
//                 'name' => 'Budget', 
//                 'description' => 'Affordable budget products'
//             ],
//             [
//                 'name' => 'Gift Idea', 
//                 'description' => 'Perfect for gifting'
//             ],
//         ];

//         foreach ($tags as $tag) {
//             Tag::create($tag);
//         }
//     }
// }



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
                'slug' => Str::slug($category['name']),
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
                'slug' => Str::slug($tagName),
            ]);
        }

        $this->command->info('✅ Categories and Tags seeded!');
    }
}