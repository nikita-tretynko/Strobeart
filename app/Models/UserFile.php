<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFile extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'file_name', 'file_patch','file_url', 'type'];

}
