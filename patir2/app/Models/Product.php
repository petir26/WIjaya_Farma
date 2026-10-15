<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug', 'name', 'category', 'package', 'price', 'stock', 'description',
        'content', 'dose', 'warning', 'image', 'requires_prescription', 'is_active',
    ];

    protected function casts(): array
    {
        return ['price' => 'decimal:2', 'dose' => 'array', 'requires_prescription' => 'boolean', 'is_active' => 'boolean'];
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
