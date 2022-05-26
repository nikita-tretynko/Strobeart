<?php

namespace App\Models;

use App\Enums\TypeBalanceEnum;
use App\Traits\DateTimeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MoneyTransfer extends Model
{
    use HasFactory, DateTimeTrait;


    protected $fillable = [
        'user_id',
        'payment_id',
        'amount',
        'net',
        'fee',
        'description',
        'status',
        'image_job_id',
        'type_balance',
        'created'
    ];

    public function payment():BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function payment_images():HasMany
    {
        return $this->hasMany(PaymentImage::class, 'image_job_id', 'image_job_id');
    }

    public function save(array $options = []): bool
    {
        $this->created = $this->getIsoDateTime();

        return parent::save($options);
    }

    public static function balance($id): array
    {
        $pending = (int)self::where('user_id', $id)->where('type_balance', TypeBalanceEnum::$PENDING)->sum('net');
        $withdrawn = self::where('user_id', $id)->where('type_balance', TypeBalanceEnum::$WITHDRAWN)->sum('amount');
        $available_balance = self::where('user_id', $id)->where('type_balance', TypeBalanceEnum::$AVAILABLE)->sum('net');
        $available = $available_balance - $withdrawn;

        return compact('pending', 'available');
    }
}
