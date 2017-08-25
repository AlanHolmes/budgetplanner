<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'description',
        'amount',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Budget relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * @author Alan Holmes
     */
    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    /**
     * Converts the amount to an integer on saving
     *
     * @param $value
     *
     * @author Alan Holmes
     */
    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = (int) $value * 100;
    }
}
