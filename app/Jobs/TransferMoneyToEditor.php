<?php

namespace App\Jobs;

use App\Enums\MoneyTransferStatusEnum;
use App\Enums\TypeBalanceEnum;
use App\Models\MoneyTransfer;
use App\Models\Payment;
use App\Models\PaymentImage;
use App\Models\StripeConnect;
use App\Services\MoneyTransferService;
use App\Services\PaymentImageService;
use App\Services\StripeService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TransferMoneyToEditor implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $image_job_id;
    private int $user_id;
    private int $payment_job_amount;
    private Payment $payment;
    private PaymentImageService $imageService;
    private MoneyTransferService $moneyTransferService;
    private StripeService $stripeService;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $image_job_id, Payment $payment, int $user_id)
    {
        $this->image_job_id = $image_job_id;
        $this->payment = $payment;
        $this->payment_job_amount = $payment->amount;
        $this->user_id = $user_id;
        $this->imageService = new PaymentImageService();
        $this->moneyTransferService = new MoneyTransferService();
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
        $price = $this->imageService->priceForImagesWorked($this->image_job_id, $this->payment_job_amount);
        $money_transfer = $this->moneyTransferService->create(
            $this->user_id, $this->payment_job_amount, MoneyTransferStatusEnum::$PAYED,
            TypeBalanceEnum::$PENDING, $price, $this->payment_job_amount - $price,
            'Payment for worked image job',
            $this->image_job_id,
            $this->payment->id
        );
        MoneyTransferToAvailableBalance::dispatch($money_transfer->id, $this->image_job_id, $this->user_id, $price)->delay(Carbon::now()->addDays(7));
    }
}
