<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserWorkJob extends Model
{
    use HasFactory;

    protected $fillable = ['image_jobs_id','user_id','status','timer','add_time'];

    public function image_job():BelongsTo
    {
        return $this->belongsTo(ImageJob::class,'image_jobs_id','id');
    }
}
