<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCard extends Model
{
    use HasFactory;

    protected $fillable=[
        'user_id',
        'customer',
        'card',
        'last4',
        'exp_year',
        'exp_month',
        'brand',
        'country',
        'address_zip',
        'is_default'
    ];
}
