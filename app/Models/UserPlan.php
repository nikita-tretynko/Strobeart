<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPlan extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'plan', 'status', 'price', 'finished_date', 'created','continuation_plan'];


    public static function gepPricePlan(): array
    {
        return ['BASIC' => 900, 'INTERMEDIATE' => 1900, 'EXPERT' => 2900];
    }

}
