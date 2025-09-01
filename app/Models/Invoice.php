<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'client_id',
        'servicio_id',
        'amount',
        'currency',
        'description',
        'status',
        'due_date',
        'stripe_session_id',
        'stripe_payment_intent',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'datetime',
    ];

    public function provider()
    {
        return $this->belongsTo(Usuario::class, 'provider_id', 'id_usuario');
    }

    public function client()
    {
        return $this->belongsTo(Usuario::class, 'client_id', 'id_usuario');
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'servicio_id', 'id_servicios_personales');
    }
}
