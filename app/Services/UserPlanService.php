<?php

namespace App\Services;

use App\Enums\UserPlansStatusEnum;
use App\Models\UserPlan;
use App\Traits\DateTimeTrait;
use Carbon\Carbon;

class UserPlanService
{
    use DateTimeTrait;

    /**
     * @param $user
     * @param $plan
     * @param null $data
     * @return UserPlan
     */
    public function createUserPlan($user, $plan, $data = null): UserPlan
    {
        $date = $this->setTimezoneDateTime(null, $data['timezone']);
        $old_plan = UserPlan::where('user_id', $user->id)
            ->where('status', UserPlansStatusEnum::$ACTIVE)->first();
        if ($old_plan) {
            $old_plan->status = UserPlansStatusEnum::$CANCELED;
            $old_plan->save();
        }
        return $this->create($user, $plan, $date);
    }

    /**
     * @param $user
     * @param $plan
     * @param $date
     * @return UserPlan
     */
    public function create($user, $plan, $date): UserPlan
    {
        return UserPlan::create([
            'user_id' => $user->id,
            'plan' => $plan,
            'continuation_plan' => 1,
            'price' => UserPlan::gepPricePlan()[$plan],
            'status' => UserPlansStatusEnum::$ACTIVE,
            'finished_date' => Carbon::parse($date)->addMonth(1)->toIso8601String(),
            'created' => $date
        ]);
    }
}
