<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ChatJob extends Model
{
    use HasFactory;

    protected $fillable = ['job_image_id', 'channels_sid', 'channels_name', 'user_editor_id', 'user_business_id', 'created'];

    public function user_business():BelongsTo
    {
        return $this->belongsTo(User::class, 'user_business_id', 'id');
    }

    public function user_editor():BelongsTo
    {
        return $this->belongsTo(User::class, 'user_editor_id', 'id');
    }

    public function job_image() :BelongsTo
    {
        return $this->belongsTo(ImageJob::class, 'job_image_id', 'id');
    }

}
