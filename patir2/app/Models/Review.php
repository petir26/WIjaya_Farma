<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['user_id', 'product_id', 'order_item_id', 'rating', 'comment'];
    protected function casts(): array { return ['rating' => 'integer']; }
    public function user() { return $this->belongsTo(User::class); }
    public function product() { return $this->belongsTo(Product::class); }
    public function orderItem() { return $this->belongsTo(OrderItem::class); }
}
