<?php


namespace App\Services;


use App\Exceptions\ApiValidationException;
use App\Jobs\ResetPasswordEmailJob;
use App\Models\PasswordReset;
use App\Models\User;
use App\Traits\DateTimeTrait;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Class AuthService
 * @package App\Services
 */
class AuthService
{

    use DateTimeTrait;

    private TwilioService $twilio_service;


    public function __construct(TwilioService $twilio_service)
    {
        $this->twilio_service = $twilio_service;
    }

    /**
     * @param $data
     * @throws ApiValidationException
     */
    public function verifySms($data)
    {
        $user = User::where('phone', $data['phone'])->first();
        if ($user && $data['code'] == $user->verification_code) {
            $user->phoneVerifiedAt();
        } else {
            throw new ApiValidationException('Wrong code. Please, try again');
        }
    }

    /**
     * @param $data
     * @return array
     */
    public function registerUser($data): array
    {
        $user = new User();
        $user->fill(collect($data)->only(['last_name', 'first_name', 'email', 'type_user', 'phone'])->toArray());
        // $user->verification_code = self::generateSmsCode($data['phone']);
        $user->name = $data['last_name'] . ' ' . $data['first_name'];
        $user->password = Hash::make($data['password']);
        $user->save();
        $token = JWTAuth::fromUser($user);
        // $this->twilio_service->sendSMS($user->phone,$user->verification_code);

        return compact('user','token');
    }

    /**
     * @param $email
     */
    public function forgotPassword($email):void
    {
        $user = User::where('email', $email)->first();
        $model = PasswordReset::updateOrCreate([
            'email' => $user->email,
        ], ['created_at' => \Carbon\Carbon::now(), 'token' => Str::random(32)]);
        $token = $model->token;
        ResetPasswordEmailJob::dispatch($user,$token);
    }

    /**
     * @param $data
     * @throws ApiValidationException
     */
    public function setNewPassword($data)
    {
        $token = $data['token'];
        $password = $data['password'];
        $model = $this->getResetTokenWithUser($token);

        if (!$model || !$model['user']) {
            throw new ApiValidationException('Link is no longer active. Please try again.');
        }

        $user = $model->user;
        $user->password = Hash::make($password);
        $user->save();
        $model->delete();
    }

    /**
     * @param string $token
     * @return PasswordReset|null
     */
    private function getResetTokenWithUser(string $token): ?PasswordReset
    {
        $model = PasswordReset::where('token', $token)
            ->with('user')
            ->first();

        $one_day_later = Carbon::now()->addDays(-1);
        $reset_time = new Carbon($model['created_at'] ?? null);
        if ($reset_time < $one_day_later) {
            $model->delete();
            $model = null;
        }

        return $model;
    }

    /**
     * @param string $token
     * @return array
     * @throws ApiValidationException
     */
    public function resetPasswordInfo(string $token): array
    {
        $model = $this->getResetTokenWithUser($token);

        if (!$model || !$model['user']) {
            throw new ApiValidationException('Link is no longer active. Please try again.');
        }

        return collect($model['user'])
            ->toArray();
    }

    /**
     * @param null $phone
     * @return string
     */
    public static function generateSmsCode($phone = null): string
    {
        if ($phone) {
            $is_ukrainian_phone = str_starts_with($phone, '80') || str_starts_with($phone, '380');
            if ($is_ukrainian_phone) {
                return '777777';
            }
        }
        $code = '';
        srand((double)microtime() * 1000000);
        for ($i = 0; $i < 6; $i++) {
            $code .= rand(0, 9);
        }
        return $code;
    }
}
