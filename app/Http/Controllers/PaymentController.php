<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $order = Order::find($request->order_id);

        if ($order->user_id !== auth('api')->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($order->status !== 'pending') {
            return response()->json(['error' => 'Order is already processed'], 400);
        }

        try {
            $payment = $this->paymentService->processPayment($order, $request->only('payment_method'));

            if ($payment) {
                return response()->json([
                    'message' => 'Payment successful',
                    'payment' => $payment
                ], 201);
            }
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        return response()->json(['error' => 'Payment failed'], 400);
    }
}
