<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkedImage extends Model
{
    use HasFactory;

    protected $fillable = ['user_work_id','image_jobs_id','image_url','status','timer','sum_timers','add_time','number_file','image_id','user_id','start_timer'];

    public function image_job():BelongsTo
    {
        return $this->belongsTo(Image::class,'image_id','id');
    }
}
