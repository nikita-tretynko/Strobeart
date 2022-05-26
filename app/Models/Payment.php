<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'client_id', 'job_image_id', 'amount', 'receipt_url', 'payment_intent_id', 'type_payment', 'status', 'money_back', 'created'];

    public function user_editor(): BelongsTo
    {
        return $this->belongsTo(User::class,'client_id','id');
    }

    public function user_business(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}
