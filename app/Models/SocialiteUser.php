<?php

namespace App\Models;

use App\Services\CryptService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialiteUser extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'type', 'login', 'password'];

    public function getPassword($key, $value): string
    {
        $cryptService = new CryptService();
        return $cryptService->decryptUserPassword($key, $value);
    }
}
