<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserWorkJob extends Model
{
    use HasFactory;

    protected $fillable = ['image_jobs_id','user_id','status','timer','add_time'];

    /**
     * @return BelongsTo
     */
    public function image_job():BelongsTo
    {
        return $this->belongsTo(ImageJob::class,'image_jobs_id','id');
    }

    /**
     * @return BelongsTo
     */
    public function user():BelongsTo
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}
