<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstagramConnects extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'token', 'instagram_id', 'page_id', 'token_expires'];

}
