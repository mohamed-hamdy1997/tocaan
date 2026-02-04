<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Str;

class CreditCardPaymentStrategy implements PaymentStrategyInterface
{
    public function process(Order $order, array $paymentData)
    {
        $paymentSuccess = true;

        if ($paymentSuccess) {
            return Payment::create([
                'payment_id' => 'CC-' . Str::random(10),
                'order_id' => $order->id,
                'status' => 'successful',
                'payment_method' => 'credit_card',
                'amount' => $order->total_price,
            ]);
        }

        return null;
    }
}
