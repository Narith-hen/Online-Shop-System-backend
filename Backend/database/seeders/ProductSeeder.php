<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Electronics', 'description' => 'Gadgets, devices, and tech accessories'],
            ['name' => 'Clothing', 'description' => 'Fashion, apparel, and accessories for all seasons'],
            ['name' => 'Home & Living', 'description' => 'Furniture, decor, and home essentials'],
            ['name' => 'Books', 'description' => 'Fiction, non-fiction, and educational books'],
        ];

        $categoryImages = [
            'Electronics' => 'https://images.unsplash.com/photo-1550009158-9ebf69173e03?w=400&h=300&fit=crop',
            'Clothing' => 'https://images.unsplash.com/photo-1445205170230-053b83016050?w=400&h=300&fit=crop',
            'Home & Living' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=400&h=300&fit=crop',
            'Books' => 'https://images.unsplash.com/photo-1495446815901-a7297e633e8d?w=400&h=300&fit=crop',
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['name' => $cat['name']],
                [
                    'description' => $cat['description'],
                    'image' => $categoryImages[$cat['name']] ?? null,
                    'is_active' => true,
                ]
            );
        }

        $categoryIds = Category::whereIn('name', array_column($categories, 'name'))->pluck('id', 'name');

        $productsByCategory = [
            'Electronics' => [
                ['name' => 'Wireless Bluetooth Headphones', 'price' => 59.99, 'stock' => 45, 'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=300&h=300&fit=crop'],
                ['name' => 'Smartphone Stand Holder', 'price' => 15.99, 'stock' => 120, 'image' => 'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=300&h=300&fit=crop'],
                ['name' => 'USB-C Charging Cable 2m', 'price' => 9.99, 'stock' => 200, 'image' => 'https://images.unsplash.com/photo-1583863788434-e58a36330cf0?w=300&h=300&fit=crop'],
                ['name' => 'Portable Power Bank 10000mAh', 'price' => 39.99, 'stock' => 75, 'image' => 'https://images.unsplash.com/photo-1609091839311-d5365f9ff1c5?w=300&h=300&fit=crop'],
                ['name' => 'Wireless Charging Pad', 'price' => 25.99, 'stock' => 90, 'image' => 'https://images.unsplash.com/photo-1622445275463-afa2ab738c34?w=300&h=300&fit=crop'],
                ['name' => 'Bluetooth Speaker', 'price' => 45.99, 'stock' => 55, 'image' => 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=300&h=300&fit=crop'],
                ['name' => 'Mechanical Keyboard', 'price' => 89.99, 'stock' => 30, 'image' => 'https://images.unsplash.com/photo-1618384887929-16ec33fab9ef?w=300&h=300&fit=crop'],
                ['name' => 'Wireless Mouse', 'price' => 29.99, 'stock' => 85, 'image' => 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=300&h=300&fit=crop'],
                ['name' => 'HDMI Cable 3m', 'price' => 12.99, 'stock' => 150, 'image' => 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=300&h=300&fit=crop'],
                ['name' => 'Laptop Sleeve 15.6"', 'price' => 34.99, 'stock' => 40, 'image' => 'https://images.unsplash.com/photo-1603302576837-37561b875f8b?w=300&h=300&fit=crop'],
            ],
            'Clothing' => [
                ['name' => 'Cotton Crew Neck T-Shirt', 'price' => 24.99, 'stock' => 80, 'image' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=300&h=300&fit=crop'],
                ['name' => 'Slim Fit Casual Jacket', 'price' => 89.99, 'stock' => 35, 'image' => 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=300&h=300&fit=crop'],
                ['name' => 'Running Sneakers', 'price' => 74.99, 'stock' => 50, 'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=300&h=300&fit=crop'],
                ['name' => 'Denim Jeans', 'price' => 59.99, 'stock' => 45, 'image' => 'https://images.unsplash.com/photo-1604176354204-9268737828e4?w=300&h=300&fit=crop'],
                ['name' => 'Wool Beanie Hat', 'price' => 19.99, 'stock' => 65, 'image' => 'https://images.unsplash.com/photo-1576871337632-b9aef4c17ab9?w=300&h=300&fit=crop'],
                ['name' => 'Leather Belt', 'price' => 34.99, 'stock' => 55, 'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=300&h=300&fit=crop'],
                ['name' => 'Summer Dress', 'price' => 44.99, 'stock' => 40, 'image' => 'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=300&h=300&fit=crop'],
                ['name' => 'Classic Polo Shirt', 'price' => 39.99, 'stock' => 70, 'image' => 'https://images.unsplash.com/photo-1598033129183-c4f50c736e10?w=300&h=300&fit=crop'],
                ['name' => 'Wool Scarf', 'price' => 29.99, 'stock' => 50, 'image' => 'https://images.unsplash.com/photo-1601925260368-ae2f83cf8b7f?w=300&h=300&fit=crop'],
                ['name' => 'Sports Shorts', 'price' => 27.99, 'stock' => 60, 'image' => 'https://images.unsplash.com/photo-1591195853828-11db59a44f6b?w=300&h=300&fit=crop'],
            ],
            'Home & Living' => [
                ['name' => 'Ceramic Coffee Mug Set (4 pcs)', 'price' => 29.99, 'stock' => 60, 'image' => 'https://images.unsplash.com/photo-1514228742587-6b1558fcca3d?w=300&h=300&fit=crop'],
                ['name' => 'Scented Soy Candle', 'price' => 18.99, 'stock' => 90, 'image' => 'https://images.unsplash.com/photo-1603006905003-be475563bc59?w=300&h=300&fit=crop'],
                ['name' => 'Throw Blanket', 'price' => 39.99, 'stock' => 35, 'image' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=300&h=300&fit=crop'],
                ['name' => 'Wall Clock Modern', 'price' => 32.99, 'stock' => 25, 'image' => 'https://images.unsplash.com/photo-1563861826100-9cb8680b0ec2?w=300&h=300&fit=crop'],
                ['name' => 'Bamboo Cutting Board', 'price' => 22.99, 'stock' => 70, 'image' => 'https://images.unsplash.com/photo-1594226801341-41427b4e5c22?w=300&h=300&fit=crop'],
                ['name' => 'Glass Food Container Set', 'price' => 27.99, 'stock' => 45, 'image' => 'https://images.unsplash.com/photo-1585238342024-78d387f4a707?w=300&h=300&fit=crop'],
                ['name' => 'Indoor Plant Pot', 'price' => 24.99, 'stock' => 40, 'image' => 'https://images.unsplash.com/photo-1485955900006-10f4d324d411?w=300&h=300&fit=crop'],
                ['name' => 'Scented Diffuser Set', 'price' => 35.99, 'stock' => 50, 'image' => 'https://images.unsplash.com/photo-1600612253971-422e7f7faeb6?w=300&h=300&fit=crop'],
                ['name' => 'Photo Frame 8x10', 'price' => 14.99, 'stock' => 100, 'image' => 'https://images.unsplash.com/photo-1585829365295-ab7cd400c167?w=300&h=300&fit=crop'],
                ['name' => 'Bath Towel Set (2 pcs)', 'price' => 32.99, 'stock' => 55, 'image' => 'https://images.unsplash.com/photo-1616627547584-bf28cee262db?w=300&h=300&fit=crop'],
            ],
            'Books' => [
                ['name' => 'The Great Adventure', 'price' => 14.99, 'stock' => 40, 'image' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=300&h=300&fit=crop'],
                ['name' => 'Learn JavaScript in 30 Days', 'price' => 34.99, 'stock' => 25, 'image' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=300&h=300&fit=crop'],
                ['name' => 'Cooking Made Easy', 'price' => 27.99, 'stock' => 35, 'image' => 'https://images.unsplash.com/photo-1589998059171-988d887df646?w=300&h=300&fit=crop'],
                ['name' => 'The Art of Mindfulness', 'price' => 19.99, 'stock' => 50, 'image' => 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=300&h=300&fit=crop'],
                ['name' => 'Space Explorers', 'price' => 22.99, 'stock' => 30, 'image' => 'https://images.unsplash.com/photo-1532012197267-da84d127e765?w=300&h=300&fit=crop'],
                ['name' => 'History of Modern Art', 'price' => 45.99, 'stock' => 15, 'image' => 'https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?w=300&h=300&fit=crop'],
                ['name' => 'DIY Home Projects', 'price' => 24.99, 'stock' => 20, 'image' => 'https://images.unsplash.com/photo-1531834685032-c34bf0d84c77?w=300&h=300&fit=crop'],
                ['name' => 'Mystery at Midnight', 'price' => 12.99, 'stock' => 45, 'image' => 'https://images.unsplash.com/photo-1621351183012-e2f9972dd9bf?w=300&h=300&fit=crop'],
                ['name' => 'Data Science for Beginners', 'price' => 39.99, 'stock' => 20, 'image' => 'https://images.unsplash.com/photo-1555949963-aa79dcee981c?w=300&h=300&fit=crop'],
                ['name' => 'Travel Guide: Europe', 'price' => 18.99, 'stock' => 30, 'image' => 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=300&h=300&fit=crop'],
            ],
        ];

        foreach ($productsByCategory as $categoryName => $products) {
            $catId = $categoryIds[$categoryName] ?? null;
            foreach ($products as $p) {
                Product::updateOrCreate(
                    ['name' => $p['name']],
                    [
                        'price' => $p['price'],
                        'stock' => $p['stock'],
                        'category_id' => $catId,
                        'image' => $p['image'] ?? null,
                        'is_active' => true,
                    ]
                );
            }
        }

        $this->command->info('4 categories and 40 products seeded successfully.');
    }
}
