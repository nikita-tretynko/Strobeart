<?php

namespace App\Services;

use App\Enums\UserPlansEnum;
use App\Enums\UserPlansStatusEnum;
use App\Exceptions\ApiValidationException;
use App\Models\Image;
use Carbon\Carbon;

class ImageJobService
{
    public function limitPlanPrice($user, $count_add_img, $editing_level): int
    {
        $user_plan = $user->plan;
        $count_img = Image::where('plan_id', $user_plan->id)->count();
//        $count_img = Image::where('user_id', $user->id)
//            ->where('created_at', '>=', Carbon::parse($user->plan->created)->setTimezone(config('app.timezone'))->toIso8601String())
//            ->where('created_at', '<=', Carbon::parse($user->plan->finished_date)->setTimezone(config('app.timezone'))->toIso8601String())
//            ->count();
        switch ($user_plan->plan) {
            case UserPlansEnum::$BASIC:
                if (($count_add_img + $count_img) > 100) {
                    throw new ApiValidationException('You got over the pictures limit with your plan');
                }
                return $this->editingLevelPrice($editing_level, UserPlansEnum::$EXPERT)*$count_add_img;
            case UserPlansEnum::$INTERMEDIATE:
                if (($count_add_img + $count_img) > 1000) {
                    throw new ApiValidationException('You got over the pictures limit with your plan');
                }
                return $this->editingLevelPrice($editing_level, UserPlansEnum::$EXPERT)*$count_add_img;

            case UserPlansEnum::$EXPERT:
                return $this->editingLevelPrice($editing_level, UserPlansEnum::$EXPERT)*$count_add_img;
        }
        return 0;
    }

    private function editingLevelPrice($editing_level, $plan): int
    {
        $price['BASIC'] = ['Simple' => 500, 'Intermediate' => 700, 'Advanced' => 900];
        $price['INTERMEDIATE'] = ['Simple' => 400, 'Intermediate' => 600, 'Advanced' => 800];
        $price['EXPERT'] = ['Simple' => 400, 'Intermediate' => 500, 'Advanced' => 700];

        return $price[$plan][$editing_level];
    }
}
