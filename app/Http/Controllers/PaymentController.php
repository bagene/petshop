<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function get($uuid)
    {
        // return failure status from order payment
        if (request()->get('status') === 'failure') {
            return response()->json([
                'message' => request()->get('message'),
            ], request()->get('code'));
        }

        $payment = Payment::whereUuid($uuid)->first();
        return response()->json($payment);
    }
}
