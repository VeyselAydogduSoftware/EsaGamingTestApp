<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CurrencyValue;

class Currency extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
        'created_by',
    ];

    protected $fillable = [
        'long_name',
        'currency_code',
        'symbol',
        'created_by',
    ];

    public function currencyValues()
    {
        return $this->hasMany(CurrencyValue::class, 'currency_id', 'id');
    }

}
