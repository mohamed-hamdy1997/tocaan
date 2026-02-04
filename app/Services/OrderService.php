<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function getUserOrders($user)
    {
        return $user->orders()->with('items')->get();
    }

    public function createOrder($user, array $items)
    {
        return DB::transaction(function () use ($user, $items) {
            $totalPrice = 0;
            foreach ($items as $item) {
                $totalPrice += $item['price'] * $item['quantity'];
            }

            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'pending',
                'total_price' => $totalPrice,
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            return $order->load('items');
        });
    }

    public function getOrderDetails($user, $id)
    {
        return $user->orders()->with('items')->find($id);
    }
}
