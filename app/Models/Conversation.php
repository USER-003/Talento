<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = [
        'user_one',
        'user_two',
        'last_message_at',
        'is_blocked',
        'blocked_by'
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'is_blocked' => 'boolean'
    ];

    public function userOne(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'user_one', 'id_usuario');
    }

    public function userTwo(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'user_two', 'id_usuario');
    }

    public function blockedBy(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'blocked_by', 'id_usuario');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function latestMessage(): HasMany
    {
        return $this->hasMany(Message::class)->latest();
    }

    public function getOtherUser($userId)
    {
        return $this->user_one === $userId ? $this->userTwo : $this->userOne;
    }

    public function isParticipant($userId): bool
    {
        return $this->user_one === $userId || $this->user_two === $userId;
    }

    public function hasUnreadMessages($userId): bool
    {
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->exists();
    }
}
