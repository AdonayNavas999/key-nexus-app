<?php

namespace Database\Seeders;

use App\Models\Credencial;
use App\Models\User;
use Illuminate\Database\Seeder;

class CredencialSeeder extends Seeder
{
    /**
     * Carga credenciales de ejemplo para el usuario principal.
     */
    public function run(): void
    {
        $usuario = User::where('email', 'adonay@keynexus.com')->first();

        if (!$usuario) {
            $this->command->warn('⚠️ No se encontró el usuario principal adonay@keynexus.com. Ejecuta primero UsuarioSeeder.');
            return;
        }

        $credenciales = [
            [
                'nombre_aplicacion' => 'Facebook',
                'correo_o_usuario'  => 'adonay.navas@facebook.com',
                'contrasena'        => 'Fb#SecurePass_2026!',
                'url'               => 'https://www.facebook.com',
                'notas'             => 'Autenticación en dos pasos (2FA) configurada con Authenticator.',
                'historial'         => [
                    [
                        'contrasena_anterior' => 'ClaveVieja_Fb2024!',
                        'contrasena_nueva'    => 'Fb#SecurePass_2026!',
                        'motivo'              => 'Actualización periódica por seguridad.',
                    ],
                ],
            ],
            [
                'nombre_aplicacion' => 'Instagram',
                'correo_o_usuario'  => '@adonay_navas',
                'contrasena'        => 'Insta$Graph_2026*X',
                'url'               => 'https://www.instagram.com',
                'notas'             => 'Perfil personal de fotografía y viajes.',
            ],
            [
                'nombre_aplicacion' => 'Steam',
                'correo_o_usuario'  => 'adonay_gamer99',
                'contrasena'        => 'St3am#Vault!Player',
                'url'               => 'https://store.steampowered.com',
                'notas'             => 'Steam Guard vinculado al móvil. Biblioteca principal de juegos.',
                'historial'         => [
                    [
                        'contrasena_anterior' => 'SteamOldPass2023#',
                        'contrasena_nueva'    => 'St3am#Vault!Player',
                        'motivo'              => 'Cambio preventivo por nuevo equipo gaming.',
                    ],
                ],
            ],
            [
                'nombre_aplicacion' => 'GitHub',
                'correo_o_usuario'  => 'adonay-navas',
                'contrasena'        => 'Gh#Dev$KeyNexus*2026',
                'url'               => 'https://github.com',
                'notas'             => 'Cuenta de desarrollador, clave SSH vinculada a Windows Terminal.',
            ],
            [
                'nombre_aplicacion' => 'Google / Gmail',
                'correo_o_usuario'  => 'adonay.work@gmail.com',
                'contrasena'        => 'G00gl3!P@ssw0rd#Pro',
                'url'               => 'https://myaccount.google.com',
                'notas'             => 'Correo corporativo y almacenamiento en Google Drive.',
            ],
            [
                'nombre_aplicacion' => 'Netflix',
                'correo_o_usuario'  => 'adonay.family@gmail.com',
                'contrasena'        => 'N3tfl!x_Streaming#26',
                'url'               => 'https://www.netflix.com',
                'notas'             => 'Plan familiar Premium 4K UHD.',
            ],
            [
                'nombre_aplicacion' => 'Spotify',
                'correo_o_usuario'  => 'adonay_music',
                'contrasena'        => 'Sp0tify$Audio#Track99',
                'url'               => 'https://www.spotify.com',
                'notas'             => 'Plan individual con listas de reproducción y podcasts de programación.',
            ],
            [
                'nombre_aplicacion' => 'Banco Agrícola (e-Banca)',
                'correo_o_usuario'  => 'adonay_navas99',
                'contrasena'        => 'B@nk#Agr1cola$2026!',
                'url'               => null, // App móvil
                'notas'             => 'Ingreso biométrico en Android. Política bancaria de rotación cada 90 días.',
                'historial'         => [
                    [
                        'contrasena_anterior' => 'B@nkAnterior2025*',
                        'contrasena_nueva'    => 'B@nk#Agr1cola$2026!',
                        'motivo'              => 'Obligatorio por vencimiento trimestral del banco.',
                    ],
                ],
            ],
        ];

        foreach ($credenciales as $item) {
            $historialItems = $item['historial'] ?? null;
            unset($item['historial']);

            // Crear o actualizar la credencial
            $credencial = Credencial::updateOrCreate(
                [
                    'usuario_id'        => $usuario->id,
                    'nombre_aplicacion' => $item['nombre_aplicacion'],
                ],
                array_merge($item, ['usuario_id' => $usuario->id])
            );

            // Si tiene historial de ejemplo, registrarlo si aún no existe
            if ($historialItems && $credencial->historial()->count() === 0) {
                foreach ($historialItems as $hist) {
                    $credencial->historial()->create($hist);
                }
            }
        }

        $this->command->info('✅ Se cargaron 8 credenciales de ejemplo (con historial de movimientos) para ' . $usuario->email);
    }
}
