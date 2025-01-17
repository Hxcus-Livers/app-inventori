<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryLog extends Model
{
    protected $fillable = ['item_id', 'action_type', 'quantity', 'notes'];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
