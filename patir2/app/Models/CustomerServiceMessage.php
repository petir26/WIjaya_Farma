<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerServiceMessage extends Model
{
    protected $fillable = ['user_id', 'message', 'admin_reply', 'status'];
    public function user() { return $this->belongsTo(User::class); }
}
