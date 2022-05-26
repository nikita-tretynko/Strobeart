<?php

namespace App\Services;

use App\Enums\WorkedImagesStatusEnum;
use App\Models\Payment;
use App\Models\PaymentImage;
use App\Models\WorkedImage;

class PaymentImageService
{
    public function create($image_jobs_id, $image_id, $amount, $commission)
    {
        PaymentImage::create([
            'image_job_id' => $image_jobs_id,
            'image_id' => $image_id,
            'amount' => $amount,
            'commission' => $commission
        ]);
    }

    public function priceForImagesWorked($image_jobs_id, $price_image_job): int
    {
        $worked_images = WorkedImage::where('image_jobs_id', $image_jobs_id)
            ->with('image_job')
            ->where('status', WorkedImagesStatusEnum::$FINISHED)->get();
        $price = 0;
        foreach ($worked_images as $image) {
            $timer = $image->timer;
            $price_for_one_image = $price_image_job / count($worked_images);
            switch ($timer) {
                case ($timer > 0 && $timer <= 300):
                    $amount = $price_for_one_image * (95 / 100);
                    break;
                case ($timer > 300 && $timer <= 600):
                    $amount = $price_for_one_image * (85 / 100);
                    break;
                case ($timer > 600 && $timer <= 900):
                    $amount = $price_for_one_image * (80 / 100);
                    break;
                case ($timer > 900 && $timer <= 1200):
                    $amount = $price_for_one_image * (50 / 100);
                    break;
                case ($timer > 1200 && $timer <= 1500):
                    $amount = $price_for_one_image * (30 / 100);
                    break;
                case ($timer > 1500 && $timer <= 1800):
                    $amount = $price_for_one_image * (10 / 100);
                    break;
                default:
                    $amount = 0;
            }
            if ($image->image_job->decline) {
                $worked_images = WorkedImage::where('image_jobs_id', $image_jobs_id)
                    ->where('image_id', $image->image_job->id)
                    ->where('status', WorkedImagesStatusEnum::$DECLINED)->first();
                if ($worked_images->add_time || ($timer > 900)) {
                    switch ($timer) {
                        case ($timer > 0 && $timer <= 300):
                            $amount = $price_for_one_image * (45 / 100);
                            break;
                        case ($timer > 300 && $timer <= 600):
                            $amount = $price_for_one_image * (25 / 100);
                            break;
                        case ($timer > 600):
                            $amount = $price_for_one_image * (5 / 100);
                            break;
                    }
                } else {
                    $amount = $price_for_one_image * (75 / 100);
                }
            }
            $price += $amount;
            $this->create($image_jobs_id, $image->id, $amount, $price_for_one_image - $amount);
        }
        return $price;
    }

}
