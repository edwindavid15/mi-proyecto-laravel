<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peluqueria extends Model
{
    use HasFactory;

    // Columnas que se pueden llenar con create() o fill()
    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
    ];

    // Opcional: si tu tabla se llama diferente a 'peluquerias'
    // protected $table = 'peluquerias';
}