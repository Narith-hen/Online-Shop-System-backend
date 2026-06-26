<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::whereHas('role', fn ($q) => $q->where('name', 'customer'))->get();
        $products = Product::all();

        if ($customers->isEmpty() || $products->isEmpty()) {
            return;
        }

        $statuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];

        // Create 15 sample orders
        for ($i = 0; $i < 15; $i++) {
            $user = $customers->random();
            $status = $statuses[array_rand($statuses)];
            $itemCount = rand(1, 4);
            $selectedProducts = $products->random($itemCount);

            $total = 0;
            $order = Order::create([
                'user_id' => $user->id,
                'status'  => $status,
                'total'   => 0, // Will be updated after items
            ]);

            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 3);
                $price = $product->price;
                $subtotal = $price * $quantity;
                $total += $subtotal;

                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                    'price'      => $price,
                ]);
            }

            $order->update(['total' => $total]);
        }
    }
}
