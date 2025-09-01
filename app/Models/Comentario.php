<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    use HasFactory;

    protected $table = 'servicio_comentarios';

    protected $fillable = [
        'servicio_id',
        'id_usuario',
        'rating',
        'comentario',
    ];

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'servicio_id', 'id_servicios_personales');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}
