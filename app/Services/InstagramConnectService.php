<?php

namespace App\Services;

use App\Exceptions\ApiValidationException;
use App\Models\InstagramConnects;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class InstagramConnectService
{

    /**
     * @throws ApiValidationException
     */
    public function updateOrCreateInstagramConnects(array $date_token, User $user,string $instagram_business_account_id)
    {
        try {
            $expires_in = $date_token['expires_in']??null;
            $token_expires = null;
            if ($expires_in) {
                $hr = $date_token['expires_in'] / 3600;
                $token_expires = (Carbon::now()->addHour($hr))->toIso8601String();
            }
            return InstagramConnects::updateOrCreate(
                ['user_id' => $user->id],
                ['token' => $date_token['access_token'],
                    'instagram_id' => $instagram_business_account_id,
                    'token_expires' => $token_expires]);
        } catch (\Exception $exception) {
            Log::info('ERROR updateOrCreateInstagramConnects ' . $exception);
            throw new ApiValidationException('Error create token: ' . $exception->getMessage());
        }

    }
}
