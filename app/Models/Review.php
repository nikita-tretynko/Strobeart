<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'to_user_id', 'message', 'rating', 'type', 'job_image_id', 'created'];

    public function user():HasOne
    {
        return $this->hasOne(User::class,'id','user_id');
    }

}
