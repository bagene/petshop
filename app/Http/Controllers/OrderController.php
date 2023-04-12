<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Traits\PaymentTraits;
use Bagene\PaymentsWrapper\Exceptions\StripeException;
use Bagene\PaymentsWrapper\Stripe\Stripe;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    use PaymentTraits;

    protected Stripe $stripe;

    public function __construct(Stripe $stripe)
    {
        $this->stripe = $stripe;
    }

    public function payOrder(PaymentRequest $request, Order $order)
    {
        // check if user created this user
        if (auth()->user()->id !== $order->user_id) {
            return response()->json([
                'message' => 'Cannot Update this order',
            ], 500);
        }

        try {
            $card = $request->validated();
            $charge = $this->createStripePayment($order, $card);
            $payment = $this->createPaymentObject($charge['source']);
    
            // update order payment and status
            $order->update([
                'payment_id' => $payment->id,
                'status' => 2,
            ]);
            $uuid = $payment->uuid;
            $status = 'success';
            return redirect("/api/payments/{$uuid}?status={$status}&gtw=stripe");
        } catch (StripeException $e) {
            $uuid = '0000';
            $status = 'failure';
            $code = $e->getCode();
            $message = $e->getMessage();
            return redirect("/api/payments/{$uuid}?status={$status}&gtw=stripe&message={$message}&code={$code}");
        }
    }
}
