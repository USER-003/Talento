<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'nombre',
        'email',
        'password',
        'is_online',
        'last_seen',
        'avatar'
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'is_online' => 'boolean',
        'last_seen' => 'datetime'
    ];

    public function servicios()
    {
        return $this->belongsToMany(Servicio::class, 'servicios_personales', 'id_usuario', 'id_servicio')
                    ->withPivot('fecha_contratacion', 'estado_contratacion');
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'user_one', 'id_usuario')
                    ->orWhere('user_two', $this->id_usuario);
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id', 'id_usuario');
    }

    public function blockedUsers(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class, 'blocked_users', 'blocker_id', 'blocked_id', 'id_usuario', 'id_usuario');
    }

    public function blockedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class, 'blocked_users', 'blocked_id', 'blocker_id', 'id_usuario', 'id_usuario');
    }

    public function isBlockedBy($userId): bool
    {
        return BlockedUser::where('blocker_id', $userId)
                         ->where('blocked_id', $this->id_usuario)
                         ->exists();
    }

    public function hasBlocked($userId): bool
    {
        return BlockedUser::where('blocker_id', $this->id_usuario)
                         ->where('blocked_id', $userId)
                         ->exists();
    }

    public function getConversationWith($userId)
    {
        return Conversation::where(function ($query) use ($userId) {
            $query->where('user_one', $this->id_usuario)
                  ->where('user_two', $userId);
        })->orWhere(function ($query) use ($userId) {
            $query->where('user_one', $userId)
                  ->where('user_two', $this->id_usuario);
        })->first();
    }

    public function setOnlineStatus($status = true): void
    {
        $this->update([
            'is_online' => $status,
            'last_seen' => now()
        ]);
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/avatars/' . $this->avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->nombre) . '&color=7F9CF5&background=EBF4FF&size=64';
    }
}
