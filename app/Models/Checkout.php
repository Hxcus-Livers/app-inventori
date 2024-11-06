<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Checkout extends Model
{
    protected $fillable = ['item_id', 'quantity', 'purpose', 'status'];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
