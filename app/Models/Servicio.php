<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Servicio extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'duracion',
        'peluquero_id',
        'peluqueria_id',
        'is_active',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'duracion' => 'integer',
        'is_active' => 'boolean',
    ];

    // Relaciones
    public function peluquero(): BelongsTo
    {
        return $this->belongsTo(User::class, 'peluquero_id');
    }

    public function peluqueria(): BelongsTo
    {
        return $this->belongsTo(Peluqueria::class);
    }

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class);
    }

    // Métodos de negocio
    public function estaDisponible(): bool
    {
        return $this->is_active && $this->peluquero && $this->peluquero->is_active;
    }

    public function puedeSerReservadoPor(User $user): bool
    {
        return $this->estaDisponible() && $user->isCliente();
    }

    public function puedeSerEditadoPor(User $user): bool
    {
        if ($user->isAdmin()) return true;
        if ($user->isPeluquero() && $this->peluquero_id === $user->id) return true;
        if ($user->isDueno() && $this->peluqueria && $this->peluqueria->owner_id === $user->id) return true;

        return false;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByPeluquero($query, int $peluqueroId)
    {
        return $query->where('peluquero_id', $peluqueroId);
    }

    public function scopeByPeluqueria($query, int $peluqueriaId)
    {
        return $query->where('peluqueria_id', $peluqueriaId);
    }

    public function scopeDisponibles($query)
    {
        return $query->active()
                    ->whereHas('peluquero', function ($q) {
                        $q->active();
                    });
    }

    // Accessors & Mutators
    public function getPrecioFormateadoAttribute(): string
    {
        return '$' . number_format($this->precio, 2);
    }

    public function getDuracionFormateadaAttribute(): string
    {
        $horas = floor($this->duracion / 60);
        $minutos = $this->duracion % 60;

        if ($horas > 0) {
            return $horas . 'h ' . $minutos . 'min';
        }

        return $minutos . ' minutos';
    }

    // Validaciones
    public static function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'precio' => 'required|numeric|min:0|max:999999.99',
            'duracion' => 'required|integer|min:1|max:480', // máximo 8 horas
            'peluquero_id' => 'required|exists:users,id',
            'peluqueria_id' => 'nullable|exists:peluquerias,id',
        ];
    }

    public static function updateRules(): array
    {
        return [
            'nombre' => 'sometimes|required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'precio' => 'sometimes|required|numeric|min:0|max:999999.99',
            'duracion' => 'sometimes|required|integer|min:1|max:480',
            'peluqueria_id' => 'nullable|exists:peluquerias,id',
        ];
    }
}
