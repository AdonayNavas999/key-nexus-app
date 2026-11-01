<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CredencialHistorial extends Model
{
    use HasFactory;

    /**
     * Nombre exacto de la tabla en español.
     */
    protected $table = 'credencial_historial';

    protected $fillable = [
        'credencial_id',
        'contrasena_anterior',
        'contrasena_nueva',
        'motivo',
    ];

    /**
     * Casts de atributos.
     * Ambas versiones de la contraseña quedan cifradas con AES-256.
     */
    protected function casts(): array
    {
        return [
            'contrasena_anterior' => 'encrypted',
            'contrasena_nueva'    => 'encrypted',
        ];
    }

    /**
     * Relación: Un movimiento de historial pertenece a una credencial.
     */
    public function credencial(): BelongsTo
    {
        return $this->belongsTo(Credencial::class, 'credencial_id');
    }
}
