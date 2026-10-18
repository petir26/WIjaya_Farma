<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedAddress extends Model
{
    protected $fillable = [
        'user_id', 'label', 'recipient_name', 'phone', 'address', 'place_id',
        'latitude', 'longitude', 'notes', 'is_default',
    ];

    protected function casts(): array
    {
        return ['latitude' => 'decimal:7', 'longitude' => 'decimal:7', 'is_default' => 'boolean'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
