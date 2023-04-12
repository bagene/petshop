<?php

namespace App\Traits;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Str;

trait PaymentTraits
{
    /**
     * Create Stripe Payment using Bagene\PaymentWrapper package
     * @param Order $order
     * @param array $card
     * @return array
     */
    protected function createStripePayment(Order $order, array $card): array
    {
        $this->stripe->authenticate();
        $charge = $this->stripe->createPayment([
            'card' => $card,
            'amount' => $order->amount,
            'currency' => 'usd',
            'description' => "Payment for order $order->id",
        ])->json();

        if (!$charge['captured']) {
            $charge = $this->stripe->captureCharge($charge['id'])
                ->json();
        }

        return $charge;
    }

    /**
     * Create Payment Object
     * @param array $paymentMethod
     * @return Payment
     */
    protected function createPaymentObject(array $paymentMethod): Payment
    {
        return Payment::create([
            'uuid' => Str::uuid(),
            'type' => 'credit_card',
            'details' => json_encode([
                'number' => "************{$paymentMethod['last4']}",
                'expire_date' => "{$paymentMethod['exp_month']}/{$paymentMethod['exp_year']}",
                'stripe_card_id' => $paymentMethod['id'],
            ]),
        ]);
    }
}