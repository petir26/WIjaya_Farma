<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ConsultationMessage extends Model
{
    protected $fillable = ['consultation_id', 'user_id', 'sender_type', 'message', 'attachment_path'];
    protected $hidden = ['attachment_path'];
    protected $appends = ['attachment_url'];

    public function consultation() { return $this->belongsTo(Consultation::class); }
    public function user() { return $this->belongsTo(User::class); }

    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->attachment_path
            ? route('client.consultations.messages.attachment', $this)
            : null;
    }
}
