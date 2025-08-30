<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlockedUser extends Model
{
    protected $fillable = [
        'blocker_id',
        'blocked_id'
    ];

    public function blocker(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'blocker_id', 'id_usuario');
    }

    public function blocked(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'blocked_id', 'id_usuario');
    }
}
