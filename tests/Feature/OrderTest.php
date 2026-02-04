<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = JWTAuth::fromUser($this->user);
    }

    public function test_user_can_create_order()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/orders', [
                'items' => [
                    [
                        'product_name' => 'Product 1',
                        'quantity' => 2,
                        'price' => 100,
                    ],
                    [
                        'product_name' => 'Product 2',
                        'quantity' => 1,
                        'price' => 50,
                    ]
                ]
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('total_price', 250);

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'total_price' => 250,
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_name' => 'Product 1',
            'quantity' => 2,
        ]);
    }

    public function test_user_can_list_orders()
    {
        Order::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/orders');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_user_can_pay_for_order_using_paypal()
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'status' => 'pending',
            'total_price' => 100,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/payments', [
                'order_id' => $order->id,
                'payment_method' => 'paypal',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('message', 'Payment successful')
            ->assertJsonPath('payment.payment_method', 'paypal');

        $this->assertEquals('confirmed', $order->fresh()->status);
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'payment_method' => 'paypal',
            'status' => 'successful',
        ]);
    }

    public function test_user_cannot_pay_with_unsupported_method()
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'status' => 'pending',
            'total_price' => 100,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/payments', [
                'order_id' => $order->id,
                'payment_method' => 'crypto',
            ]);

        $response->assertStatus(400)
            ->assertJsonPath('error', 'Payment method [crypto] is not supported.');
    }
}
