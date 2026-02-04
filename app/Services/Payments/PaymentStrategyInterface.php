<?php

namespace App\Services\Payments;

use App\Models\Order;

interface PaymentStrategyInterface
{
    public function process(Order $order, array $paymentData);
}
