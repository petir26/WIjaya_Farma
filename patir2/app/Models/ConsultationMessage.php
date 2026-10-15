<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultationMessage extends Model
{
    protected $fillable = ['consultation_id', 'user_id', 'sender_type', 'message'];
    public function consultation() { return $this->belongsTo(Consultation::class); }
    public function user() { return $this->belongsTo(User::class); }
}
