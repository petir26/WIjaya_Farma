<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['order_number', 'user_id', 'total_amount', 'status', 'fulfillment_method', 'shipping_address', 'pharmacy_branch', 'payment_method', 'payment_status', 'payment_reference'];

    protected function casts(): array { return ['total_amount' => 'decimal:2']; }
    public function user() { return $this->belongsTo(User::class); }
    public function items() { return $this->hasMany(OrderItem::class); }
    public function shipment() { return $this->hasOne(Shipment::class); }
    public function prescriptions() { return $this->hasMany(Prescription::class); }
}
