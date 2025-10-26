<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'item_code',
        'item_name',
        'description',
        'category',
        'quantity',
        'unit_price',
        'reorder_level',
        'status'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity', '<=', 'reorder_level');
    }

    public function getTotalValueAttribute()
    {
        return $this->quantity * $this->unit_price;
    }

    public function isLowStock()
    {
        return $this->quantity <= $this->reorder_level;
    }
}