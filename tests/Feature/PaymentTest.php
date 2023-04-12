<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Traits\PaymentTraits;
use Bagene\PaymentsWrapper\Stripe\Stripe;
use Tests\TestCase;
use Illuminate\Support\Str;

class PaymentTest extends TestCase
{
    use PaymentTraits;
    protected ?Stripe $stripe;

    /**
	 * Define environment setup.
	 *
	 * @param  \Illuminate\Foundation\Application  $app
	 * @return void
	 */
	protected function defineEnvironment($app)
	{
		// setup stripe config
		$app['config']->set('payments.stripe', [
			'public_key' => env('STRIPE_PUBLIC_KEY'),
			'secret' => env('STRIPE_SECRET'),
		]);
	}

    protected function initOrderStatus()
    {
        \App\Models\OrderStatus::create(['title' => 'Pending', 'uuid' => Str::uuid()]);
        \App\Models\OrderStatus::create(['title' => 'Paid', 'uuid' => Str::uuid()]);
    }

    /** @test */
    public function it_can_create_stripe_payment()
    {
        $this->initOrderStatus();
        $this->stripe = new Stripe();

        User::factory()->hasOrders()->create();

        $order = Order::latest()->first();

        $res = $this->createStripePayment($order, [
            'number' => '4242424242424242',
            'exp_month' => 4,
            'exp_year' => 2024,
            'cvc' => '314',
        ]);

        $this->assertIsArray($res);
        $this->assertArrayHasKey('id', $res);
        $this->assertStringStartsWith('ch_', $res['id']);
    }

    /** @test */
    public function it_can_create_payment_object()
    {
        $this->initOrderStatus();
        $this->stripe = new Stripe();

        User::factory()->hasOrders()->create();

        $order = Order::latest()->first();

        $payment = $this->createStripePayment($order, [
            'number' => '4242424242424242',
            'exp_month' => 4,
            'exp_year' => 2024,
            'cvc' => '314',
        ]);

        $res = $this->createPaymentObject($payment['source']);

        $this->assertInstanceOf(Payment::class, $res);
        $this->assertJsonStringEqualsJsonString(
            '{"number": "************4242", "expire_date": "4/2024", "stripe_card_id": "'. $payment['source']['id'] .'"}',
            $res->details,
        );
    }

    /** @test */
    public function it_can_get_payment()
    {
        User::factory()
            ->create([
                'email' => 'admin@example.org',
            ]);
        
        $res = $this->post('/api/login', [
            'email' => 'admin@example.org',
            'password' => 'userpassword',
        ]);
        $token = $res->json('token');

        Payment::create([
            'uuid' => Str::uuid(),
            'type' => 'credit_card',
            'details' => '{}',
        ]);
        $payment = Payment::latest()->first();

        $res = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/payments/$payment->uuid");

        $res->assertStatus(200);
        $this->assertEquals($payment->toArray(), $res->json());
    }
}