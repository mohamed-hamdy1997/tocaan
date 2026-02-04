<?php

namespace App\Services;

use App\Models\Order;
use App\Services\Payments\CreditCardPaymentStrategy;
use App\Services\Payments\PayPalPaymentStrategy;
use App\Services\Payments\PaymentStrategyInterface;
use InvalidArgumentException;

class PaymentService
{
    protected $strategies = [
        'paypal' => PayPalPaymentStrategy::class,
        'credit_card' => CreditCardPaymentStrategy::class,
    ];

    public function processPayment(Order $order, array $paymentData)
    {
        $method = $paymentData['payment_method'];

        if (!isset($this->strategies[$method])) {
            throw new InvalidArgumentException("Payment method [{$method}] is not supported.");
        }

        $strategyClass = $this->strategies[$method];
        $strategy = new $strategyClass();

        $payment = $strategy->process($order, $paymentData);

        if ($payment && $payment->status === 'successful') {
            $order->update(['status' => 'confirmed']);
            return $payment;
        }

        return null;
    }
}
