<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'uploaded_logo',
        'upload_watermark',
        'video_instructions'
    ];
}
