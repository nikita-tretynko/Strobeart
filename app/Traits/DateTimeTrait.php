<?php


namespace App\Traits;


use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Trait DateTimeTrait
 * @package App\Traits
 */
trait DateTimeTrait
{

    /**
     * @param null $time
     * @return string|null
     */
    public function getIsoDateTime($time = null): ?string
    {
        try {
            return (new Carbon($time))
                ->setTimezone(config('app.timezone'))
                ->toIso8601String();
        } catch (\Exception $exception) {
            Log::info($exception);
            return null;
        }
    }

    public function setTimezoneDateTime($time = null,$timezone): ?string
    {
        try {
            return (new Carbon($time))
                ->shiftTimezone($timezone)
                ->toIso8601String();
        } catch (\Exception $exception) {
            Log::info($exception);
            return null;
        }
    }

}
