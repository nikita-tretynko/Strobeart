<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StyleGuide extends Model
{
    use HasFactory;

    public $fillable = [
        'user_id',
        'name',
        'remove_background',
        'size_with',
        'size_height',
        'unit_measurement',
        'file_format',
        'color_profile',
        'resolution',
        'resolution_units',
        'other',
        'file_id_logo',
        'file_id_watermark',
        'file_id_video_instructions',
        'file_id_color_palette',
        'file_id_video_typography'
    ];

    /**
     * @return BelongsTo
     */
    public function file_logo(): BelongsTo
    {
        return $this->belongsTo(UserFile::class,'file_id_logo', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function file_watermark(): BelongsTo
    {
        return $this->belongsTo(UserFile::class,'file_id_watermark', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function file_video_instructions(): BelongsTo
    {
        return $this->belongsTo(UserFile::class,'file_id_video_instructions', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function file_id_color_palette(): BelongsTo
    {
        return $this->belongsTo(UserFile::class,'file_id_color_palette', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function file_id_video_typography(): BelongsTo
    {
        return $this->belongsTo(UserFile::class,'file_id_video_typography', 'id');
    }

}
