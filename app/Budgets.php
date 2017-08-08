<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Budgets extends Model
{
    protected $fillable = [
        'name',
        'budget',
        'description'
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
}
