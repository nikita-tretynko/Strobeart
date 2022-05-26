<?php

namespace App\Services;

use App\Enums\PaymentTypeEnum;
use App\Models\Payment;
use App\Models\UserPlan;

class PaymentService
{

    /**
     * @param $payment
     * @param $user
     * @param $price
     * @param null $type_payment
     * @return mixed
     */
    public function createPayment($payment, $user, $price, $type_payment = null)
    {
        return Payment::create([
            'user_id' => $user->id,
            'amount' => $price,
            'payment_intent_id' => $payment->id,
            'type_payment' => $type_payment,
            'status' => $payment->status
        ]);
    }
}
