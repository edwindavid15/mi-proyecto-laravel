<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peluqueria extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'direccion',
        'telefono',
        'latitud',
        'longitud',
        'owner_id',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function peluqueros()
    {
        return $this->belongsToMany(User::class, 'peluqueria_user', 'peluqueria_id', 'user_id');
    }

    public function servicios()
    {
        return $this->hasMany(Servicio::class);
    }

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }
}