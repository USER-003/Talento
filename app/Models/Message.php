<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class Message extends Model
{
    protected $table = 'chat_messages';
    
    protected $fillable = [
        'conversation_id',
        'sender_id',
        'message',
        'encrypted_message',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($message) {
            // Encrypt the message before saving
            $message->encrypted_message = Crypt::encrypt($message->message);
        });
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'sender_id', 'id_usuario');
    }

    public function getDecryptedMessageAttribute(): string
    {
        try {
            return Crypt::decrypt($this->encrypted_message);
        } catch (\Exception $e) {
            return $this->message; // Fallback to unencrypted message
        }
    }

    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        }
    }
}
