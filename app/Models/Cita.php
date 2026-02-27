<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id', 'peluquero_id', 'peluqueria_id', 'servicio_id',
        'fecha', 'hora', 'estado'
    ];

    public function cliente() {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function peluquero() {
        return $this->belongsTo(User::class, 'peluquero_id');
    }

    public function peluqueria() {
        return $this->belongsTo(Peluqueria::class);
    }

    public function servicio() {
        return $this->belongsTo(Servicio::class);
    }
}
