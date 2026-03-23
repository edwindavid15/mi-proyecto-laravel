<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string|null $name
 * @property string|null $email
 * @property string|null $role
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'is_active',
        'latitud',
        'longitud',
        'is_online',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_online' => 'boolean',
            'latitud' => 'decimal:7',
            'longitud' => 'decimal:7',
        ];
    }

    // Constantes para roles
    const ROLE_CLIENTE = 'cliente';
    const ROLE_PELUQUERO = 'peluquero';
    const ROLE_DUENO = 'dueno';
    const ROLE_ADMIN = 'admin';

    const ROLES = [
        self::ROLE_CLIENTE,
        self::ROLE_PELUQUERO,
        self::ROLE_DUENO,
        self::ROLE_ADMIN,
    ];

    // Métodos de verificación de roles
    public function isCliente(): bool
    {
        return $this->role === self::ROLE_CLIENTE;
    }

    public function isPeluquero(): bool
    {
        return $this->role === self::ROLE_PELUQUERO;
    }

    public function isDueno(): bool
    {
        return $this->role === self::ROLE_DUENO;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    // Relaciones
    public function citasComoCliente(): HasMany
    {
        return $this->hasMany(Cita::class, 'cliente_id');
    }

    public function citasComoPeluquero(): HasMany
    {
        return $this->hasMany(Cita::class, 'peluquero_id');
    }

    public function servicios(): HasMany
    {
        return $this->hasMany(Servicio::class, 'peluquero_id');
    }

    public function peluquerias(): BelongsToMany
    {
        return $this->belongsToMany(Peluqueria::class, 'peluqueria_user', 'user_id', 'peluqueria_id');
    }

    public function ownedPeluquerias(): HasMany
    {
        return $this->hasMany(Peluqueria::class, 'owner_id');
    }

    // Métodos de negocio
    public function puedeCrearServicio(): bool
    {
        return $this->isPeluquero() || $this->isDueno() || $this->isAdmin();
    }

    public function puedeCrearPeluqueria(): bool
    {
        return $this->isDueno() || $this->isAdmin();
    }

    public function puedeVerTodasLasCitas(): bool
    {
        return $this->isAdmin();
    }

    public function puedeGestionarPeluqueria(int $peluqueriaId): bool
    {
        if ($this->isAdmin()) return true;
        if ($this->isDueno()) {
            return $this->ownedPeluquerias()->where('id', $peluqueriaId)->exists();
        }
        return false;
    }

    public function puedeGestionarServicio(int $servicioId): bool
    {
        if ($this->isAdmin()) return true;

        $servicio = Servicio::find($servicioId);
        if (!$servicio) return false;

        if ($this->isPeluquero()) {
            return $servicio->peluquero_id === $this->id;
        }

        if ($this->isDueno()) {
            return $this->ownedPeluquerias()->where('id', $servicio->peluqueria_id)->exists();
        }

        return false;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    public function scopePeluqueros($query)
    {
        return $query->where('role', self::ROLE_PELUQUERO);
    }

    public function scopeDuenos($query)
    {
        return $query->where('role', self::ROLE_DUENO);
    }

    public function scopeClientes($query)
    {
        return $query->where('role', self::ROLE_CLIENTE);
        
    }}