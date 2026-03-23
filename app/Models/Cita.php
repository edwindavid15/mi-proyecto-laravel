<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * @property Carbon|null $fecha
 * @property Carbon|null $hora
 */
class Cita extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'peluquero_id',
        'peluqueria_id',
        'servicio_id',
        'fecha',
        'hora',
        'estado',
        'notas',
        'precio_final',
    ];

    /**
     * @var array<string,string>
     * @psalm-var array{fecha:string, hora:string, precio_final:string}
     * @phpstan-var array<string,string>
     */
    protected $casts = [
        'fecha' => 'date:Y-m-d',
        'hora' => 'date:H:i',
        'precio_final' => 'decimal:2',
    ];

    // Constantes para estados
    const ESTADO_PENDIENTE = 'pendiente';
    const ESTADO_CONFIRMADA = 'confirmada';
    const ESTADO_EN_PROCESO = 'en_proceso';
    const ESTADO_COMPLETADA = 'completada';
    const ESTADO_CANCELADA = 'cancelada';
    const ESTADO_NO_ASISTIO = 'no_asistio';

    const ESTADOS = [
        self::ESTADO_PENDIENTE,
        self::ESTADO_CONFIRMADA,
        self::ESTADO_EN_PROCESO,
        self::ESTADO_COMPLETADA,
        self::ESTADO_CANCELADA,
        self::ESTADO_NO_ASISTIO,
    ];

    const ESTADOS_ACTIVOS = [
        self::ESTADO_PENDIENTE,
        self::ESTADO_CONFIRMADA,
        self::ESTADO_EN_PROCESO,
    ];

    // Relaciones
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function peluquero(): BelongsTo
    {
        return $this->belongsTo(User::class, 'peluquero_id');
    }

    public function peluqueria(): BelongsTo
    {
        return $this->belongsTo(Peluqueria::class);
    }

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class);
    }

    // Métodos de negocio
    public function estaActiva(): bool
    {
        return in_array($this->estado, self::ESTADOS_ACTIVOS);
    }

    public function puedeSerCanceladaPor(User $user): bool
    {
        if ($this->estado === self::ESTADO_COMPLETADA || $this->estado === self::ESTADO_CANCELADA) {
            return false;
        }

        $userId = $user->getKey();

        // El cliente puede cancelar sus propias citas
        if ($user->isCliente() && $this->cliente_id === $userId) {
            return true;
        }

        // El peluquero puede cancelar citas asignadas
        if ($user->isPeluquero() && $this->peluquero_id === $userId) {
            return true;
        }

        // El dueño puede cancelar citas de sus peluquerías
        if ($user->isDueno() && $this->peluqueria && $this->peluqueria->owner_id === $userId) {
            return true;
        }

        // Admin puede cancelar cualquier cita
        if ($user->isAdmin()) {
            return true;
        }

        return false;
    }

    public function puedeSerEditadaPor(User $user): bool
    {
        if ($this->estado === self::ESTADO_COMPLETADA || $this->estado === self::ESTADO_CANCELADA) {
            return false;
        }

        $userId = $user->getKey();

        // El peluquero asignado puede editar
        if ($user->isPeluquero() && $this->peluquero_id === $userId) {
            return true;
        }

        // El dueño puede editar citas de sus peluquerías
        if ($user->isDueno() && $this->peluqueria && $this->peluqueria->owner_id === $userId) {
            return true;
        }

        // Admin puede editar cualquier cita
        if ($user->isAdmin()) {
            return true;
        }

        return false;
    }

    public function puedeVerDetalles(User $user): bool
    {
        $userId = $user->getKey();

        // El cliente puede ver sus propias citas
        if ($user->isCliente() && $this->cliente_id === $userId) {
            return true;
        }

        // El peluquero puede ver citas asignadas
        if ($user->isPeluquero() && $this->peluquero_id === $userId) {
            return true;
        }

        // El dueño puede ver citas de sus peluquerías
        if ($user->isDueno() && $this->peluqueria && $this->peluqueria->owner_id === $userId) {
            return true;
        }

        // Admin puede ver todas las citas
        if ($user->isAdmin()) {
            return true;
        }

        return false;
    }

    private function getFechaHoraCarbon(): Carbon
    {
        $fecha = $this->fecha instanceof Carbon ? $this->fecha : Carbon::parse($this->fecha);
        $hora = $this->hora instanceof Carbon ? $this->hora : Carbon::parse($this->hora);

        return Carbon::createFromFormat('Y-m-d H:i', $fecha->format('Y-m-d') . ' ' . $hora->format('H:i'));
    }

    public function estaEnElPasado(): bool
    {
        return $this->getFechaHoraCarbon()->isPast();
    }

    public function puedeSerConfirmada(): bool
    {
        return $this->estado === self::ESTADO_PENDIENTE && !$this->estaEnElPasado();
    }

    public function puedeSerCompletada(): bool
    {
        return in_array($this->estado, [self::ESTADO_CONFIRMADA, self::ESTADO_EN_PROCESO]);
    }

    // Métodos de cambio de estado
    public function confirmar(): bool
    {
        if ($this->puedeSerConfirmada()) {
            $this->estado = self::ESTADO_CONFIRMADA;
            return $this->save();
        }
        return false;
    }

    public function iniciar(): bool
    {
        if ($this->estado === self::ESTADO_CONFIRMADA) {
            $this->estado = self::ESTADO_EN_PROCESO;
            return $this->save();
        }
        return false;
    }

    public function completar(): bool
    {
        if ($this->puedeSerCompletada()) {
            $this->estado = self::ESTADO_COMPLETADA;
            $this->precio_final = $this->servicio ? $this->servicio->precio : $this->precio_final;
            return $this->save();
        }
        return false;
    }

    public function cancelar(): bool
    {
        if (!in_array($this->estado, [self::ESTADO_COMPLETADA, self::ESTADO_CANCELADA])) {
            $this->estado = self::ESTADO_CANCELADA;
            return $this->save();
        }
        return false;
    }

    public function marcarNoAsistio(): bool
    {
        if ($this->estado === self::ESTADO_CONFIRMADA && $this->estaEnElPasado()) {
            $this->estado = self::ESTADO_NO_ASISTIO;
            return $this->save();
        }
        return false;
    }

    // Scopes
    public function scopeActivas($query)
    {
        return $query->whereIn('estado', self::ESTADOS_ACTIVOS);
    }

    public function scopeByCliente($query, int $clienteId)
    {
        return $query->where('cliente_id', $clienteId);
    }

    public function scopeByPeluquero($query, int $peluqueroId)
    {
        return $query->where('peluquero_id', $peluqueroId);
    }

    public function scopeByPeluqueria($query, int $peluqueriaId)
    {
        return $query->where('peluqueria_id', $peluqueriaId);
    }

    public function scopeByEstado($query, string $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopeProximas($query)
    {
        return $query->where('fecha', '>=', now()->toDateString())
                    ->orderBy('fecha')
                    ->orderBy('hora');
    }

    public function scopeHoy($query)
    {
        return $query->where('fecha', now()->toDateString());
    }

    // Accessors
    public function getFechaHoraAttribute(): Carbon
    {
        return $this->getFechaHoraCarbon();
    }

    public function getFechaHoraFormateadaAttribute(): string
    {
        $fecha = $this->fecha instanceof Carbon ? $this->fecha : Carbon::parse($this->fecha);
        $hora = $this->hora instanceof Carbon ? $this->hora : Carbon::parse($this->hora);

        return $fecha->format('d/m/Y') . ' ' . $hora->format('H:i');
    }

    public function getEstadoFormateadoAttribute(): string
    {
        return match($this->estado) {
            self::ESTADO_PENDIENTE => 'Pendiente',
            self::ESTADO_CONFIRMADA => 'Confirmada',
            self::ESTADO_EN_PROCESO => 'En Proceso',
            self::ESTADO_COMPLETADA => 'Completada',
            self::ESTADO_CANCELADA => 'Cancelada',
            self::ESTADO_NO_ASISTIO => 'No Asistió',
            default => 'Desconocido'
        };
    }

    // Validaciones
    public static function rules(): array
    {
        return [
            'cliente_id' => 'required|exists:users,id',
            'peluquero_id' => 'required|exists:users,id',
            'peluqueria_id' => 'required|exists:peluquerias,id',
            'servicio_id' => 'required|exists:servicios,id',
            'fecha' => 'required|date|after:today',
            'hora' => 'required|date_format:H:i',
            'estado' => 'sometimes|in:' . implode(',', self::ESTADOS),
            'notas' => 'nullable|string|max:1000',
        ];
    }

    public static function updateRules(): array
    {
        return [
            'estado' => 'sometimes|in:' . implode(',', self::ESTADOS),
            'notas' => 'nullable|string|max:1000',
            'precio_final' => 'sometimes|numeric|min:0',
        ];
    }
}
