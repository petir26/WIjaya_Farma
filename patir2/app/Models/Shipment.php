<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $fillable = ['order_id', 'address', 'status', 'courier', 'tracking_number', 'shipped_at'];
    protected function casts(): array { return ['shipped_at' => 'datetime']; }
    public function order() { return $this->belongsTo(Order::class); }
}
