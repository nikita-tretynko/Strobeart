<?php

namespace App\Models;

use App\Traits\DateTimeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublishingFiles extends Model
{
    use HasFactory, DateTimeTrait;

    protected $fillable = [
        'user_id',
        'image_jobs_id',
        'description',
        'type',
        'alt_text',
        'hashtags',
        'link',
        'auto_post_facebook',
        'tag_users',
        'tag_products',
        'search_tag',
        'pin_location',
        'date_publication',
        'created',
        'platform',
        'sequence_pictures',
        'count_posts'
    ];

    protected $casts = [
        'sequence_pictures' => 'array',
    ];

    public function save(array $options = []): bool
    {
        $this->created = $this->getIsoDateTime();

        return parent::save($options);
    }
}
