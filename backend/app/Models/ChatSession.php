<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ChatSession extends Model
{
    protected $fillable = [
        'session_id',
        'visitor_name',
        'status',
    ];

    protected static function booted(): void
    {
        static::creating(function (ChatSession $session) {
            if (empty($session->session_id)) {
                $session->session_id = Str::uuid()->toString();
            }
        });
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }
}