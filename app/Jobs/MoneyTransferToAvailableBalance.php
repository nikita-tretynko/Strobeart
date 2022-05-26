<?php

namespace App\Jobs;

use App\Enums\TypeBalanceEnum;
use App\Models\MoneyTransfer;
use App\Models\StripeConnect;
use App\Services\StripeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MoneyTransferToAvailableBalance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $money_transfer_id;
    private int $image_job_id;
    private int $user_id;
    private StripeService $stripeService;
    private int $price;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($money_transfer_id, int $image_job_id, int $user_id,int $price)
    {
        $this->money_transfer_id = $money_transfer_id;
        $this->image_job_id = $image_job_id;
        $this->user_id = $user_id;
        $this->price = $price;
        $this->stripeService = new StripeService();
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function handle()
    {
        $money_transfer = MoneyTransfer::where('type_balance', TypeBalanceEnum::$PENDING)
            ->find($this->money_transfer_id);
        if ($money_transfer && $money_transfer->type_balance) {
            $money_transfer->type_balance = TypeBalanceEnum::$AVAILABLE;
            $money_transfer->save();
        }
        $stripe_connect = StripeConnect::where('user_id', $this->user_id)->first();
        if ($stripe_connect) {
            $this->stripeService->transfer_money($this->price, $stripe_connect->connect_id, 'Payment for worked image job id:' . $this->image_job_id);
        }
    }
}
