<?php

namespace App\Models;

use App\Enums\WorkedImagesStatusEnum;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Image extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'images';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'image_job_id',
        'src',
        'approval',
        'src_cropped',
        'decline',
        'message',
        'user_id',
        'plan_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'approval' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Image job
     *
     * @return BelongsTo
     */
    public function image_job(): BelongsTo
    {
        return $this->belongsTo(ImageJob::class);
    }

    public function worked_img(): HasOne
    {
        return $this->hasOne(WorkedImage::class,'image_id','id')
            ->where('status',WorkedImagesStatusEnum::$FINISHED);
    }

    /**
     * Prepare a date
     *
     * @param  DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return Carbon::parse($date)->setTimezone('UTC')->toIso8601String();
    }
}
