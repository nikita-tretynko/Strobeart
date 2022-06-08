<?php

namespace App\Models;

use App\Enums\WorkJobStatusEnum;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ImageJob extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'image_jobs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'editing_level',
        'style_guide',
        'color_profile',
        'file_format',
        'other',
        'add_logo',
        'add_watermark',
        'white_background',
        'red_eye',
        'due_date',
        'status',
        'color_palette',
        'typography',
        'file_id_video_instruction',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'white_background' => 'boolean',
        'red_eye' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * User
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function user_work(): HasOne
    {
        return $this->hasOne(UserWorkJob::class, 'image_jobs_id', 'id');
    }

    /**
     * Images
     *
     * @return HasMany
     */
    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }

    public function images_decline(): HasMany
    {
        return $this->hasMany(Image::class)->where('decline', '!=', 0);
    }

    /**
     * Prepare a date
     *
     * @param DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return Carbon::parse($date)->setTimezone('UTC')->toIso8601String();
    }

    public function finished_worked_images(): HasMany
    {
        return $this->hasMany(WorkedImage::class, 'image_jobs_id', 'id')
            ->where('status', WorkJobStatusEnum::$FINISHED);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class, 'job_image_id');
    }

    /**
     * @return BelongsTo
     */
    public function file_logo(): BelongsTo
    {
        return $this->belongsTo(UserFile::class,'add_logo', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function file_watermark(): BelongsTo
    {
        return $this->belongsTo(UserFile::class,'add_watermark', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function file_video_instructions(): BelongsTo
    {
        return $this->belongsTo(UserFile::class,'file_id_video_instruction', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function file_color_palette(): BelongsTo
    {
        return $this->belongsTo(UserFile::class,'color_palette', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function file_typography(): BelongsTo
    {
        return $this->belongsTo(UserFile::class,'typography', 'id');
    }
}
