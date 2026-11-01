<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Credencial extends Model
{
    use HasFactory;

    /**
     * Nombre exacto de la tabla en español.
     */
    protected $table = 'credencial';

    protected $fillable = [
        'usuario_id',
        'nombre_aplicacion',
        'correo_o_usuario',
        'contrasena',
        'url',
        'notas',
    ];

    /**
     * Casts de atributos.
     * El casting 'encrypted' cifra con AES-256 al guardar y descifra automáticamente al consultar.
     */
    protected function casts(): array
    {
        return [
            'contrasena' => 'encrypted',
        ];
    }

    /**
     * Relación: Una credencial pertenece a un usuario.
     * Se especifica la FK en español 'usuario_id'.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Relación: Una credencial tiene un historial completo de cambios de contraseña.
     */
    public function historial(): HasMany
    {
        return $this->hasMany(CredencialHistorial::class, 'credencial_id')->latest();
    }
}
