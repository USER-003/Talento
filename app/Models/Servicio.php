<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
   
    protected $table = 'servicios_personales';
    protected $primaryKey = 'id_servicios_personales';

    protected $fillable = [
        'id_categoria', 'id_usuario', 'nombre_servicio', 'descripcion_servicio', 'precio', 'imagen', 'numero_contacto',
    ];

    public function categoria()
    {
        return $this->belongsTo(CategoriasDeServicio::class, 'id_categoria');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function comentarios()
    {
        return $this->hasMany(Comentario::class, 'servicio_id', 'id_servicios_personales')->latest();
    }

    public function averageRating(): float
    {
        $avg = $this->comentarios()->avg('rating');
        return $avg ? round((float) $avg, 1) : 0.0;
    }
}
