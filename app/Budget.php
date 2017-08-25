<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $fillable = [
        'name',
        'budget',
        'description',
        'frequency',
        'start_on',
    ];

    /**
     * User relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * @author Alan Holmes
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Returns the budget amount as a float
     *
     * @return string
     * @author Alan Holmes
     */
    public function getBudgetAsFloatAttribute()
    {
        return number_format($this->budget / 100, 2, '.', '');
    }
}
