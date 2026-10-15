<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    protected $fillable = ['user_id', 'product_id', 'order_id', 'file_path', 'original_name', 'mime_type', 'verification_status'];

    public function user() { return $this->belongsTo(User::class); }
    public function product() { return $this->belongsTo(Product::class); }
    public function order() { return $this->belongsTo(Order::class); }
}
