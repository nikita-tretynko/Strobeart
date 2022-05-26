<?php

namespace App\Services;

use App\Enums\MoneyTransferStatusEnum;
use App\Enums\TypeBalanceEnum;
use App\Http\Responses\ApiErrorResponse;
use App\Models\MoneyTransfer;
use App\Models\User;

class MoneyTransferService
{
    /**
     * @param int $user_id
     * @param $payment_id
     * @param $amount
     * @param $status
     * @param $type_balance
     * @param null $net
     * @param null $fee
     * @param string $description
     * @param null $image_job_id
     * @return mixed
     */
    public function create(int $user_id, $amount, $status, $type_balance, $net = null, $fee = null, $description = '', $image_job_id = null, $payment_id = null)
    {
        return MoneyTransfer::create([
            'user_id' => $user_id,
            'payment_id' => $payment_id,
            'amount' => $amount,
            'net' => $net,
            'fee' => $fee,
            'description' => $description,
            'status' => $status,
            'image_job_id' => $image_job_id,
            'type_balance' => $type_balance,
        ]);
    }

    public function isMoneyForUser(User $user, int $amount): bool
    {
        $sum_money = $user->money_transfers_done()
            ->where('type_balance', TypeBalanceEnum::$AVAILABLE)
            ->sum('net');
        if ($sum_money < $amount) {
            return false;
        }
        return true;
    }

}
