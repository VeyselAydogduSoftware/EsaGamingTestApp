<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Currency;
class CurrencyValue extends Model
{
    use HasFactory, HasUuids;
    protected $keyType = 'string';
    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'currency_id',
        'currency_value',
        'logged_at',
        'created_by',
        'updated_by'
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'id');
    }

}
