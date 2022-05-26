<?php

namespace App\Services;

use App\Models\KeysUser;
use App\Models\SocialiteUser;

class SocialiteUserService
{
    public function create($user, $type, $login, $password)
    {
        $cryptService = new CryptService();
        $password = $cryptService->encryptUserPassword($password)['password_hash'];
        $key = $cryptService->encryptUserPassword($password)['key'];
        KeysUser::create(['user_id' => $user->id, 'key' => $key, 'type' => $type]);
        return SocialiteUser::create([
            'user_id' => $user->id,
            'login' => $login,
            'password' => $password,
            'type' => $type]);
    }
}
