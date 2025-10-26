<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_id',
        'transaction_type',
        'quantity',
        'unit_price',
        'total_amount',
        'reference_number',
        'notes',
        'processed_by',
        'processed_at'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'processed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function scopeInbound($query)
    {
        return $query->where('transaction_type', 'inbound');
    }

    public function scopeOutbound($query)
    {
        return $query->where('transaction_type', 'outbound');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }
}