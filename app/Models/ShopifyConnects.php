<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopifyConnects extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','shop', 'token'];
}
