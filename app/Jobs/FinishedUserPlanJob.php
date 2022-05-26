<?php

namespace App\Jobs;

use App\Enums\PaymentTypeEnum;
use App\Enums\UserPlansStatusEnum;
use App\Models\User;
use App\Models\UserPlan;
use App\Services\PaymentService;
use App\Services\StripeService;
use App\Services\UserPlanService;
use App\Traits\DateTimeTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FinishedUserPlanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use DateTimeTrait;

    protected UserPlan $user_plan;
    private UserPlanService $planService;
    private StripeService $stripeService;
    private PaymentService $paymentService;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(UserPlan $user_plan)
    {
        $this->user_plan = $user_plan;
        $this->planService = new  UserPlanService();
        $this->stripeService = new StripeService();
        $this->paymentService = new PaymentService();
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \App\Exceptions\ApiValidationException
     */
    public function handle()
    {
        $this->user_plan->update([
            'status' => UserPlansStatusEnum::$FINISHED
        ]);
        $user = User::find($this->user_plan->user_id);
        $date = $this->getIsoDateTime();
        if ($this->user_plan->continuation_plan) {
            try {
                $amount = UserPlan::gepPricePlan()[$this->user_plan->plan];
                $payment_intent = $this->stripeService->paymentIntents($user, $amount, 'Payment MEMBERSHIP');
                $payment = $this->stripeService->confirmPaymentIntents($payment_intent->id, $user);
                $data['plan'] = $this->user_plan->plan;
                $this->paymentService->createPayment($payment, $user,$amount, PaymentTypeEnum::$MEMBERSHIP);
                $this->planService->create($user, $this->user_plan->plan, $date);
            } catch (\Exception $exception) {
                Log::info('!!ERROR FinishedUserPlanJob: ' . $exception);
            }
        }
    }
}
