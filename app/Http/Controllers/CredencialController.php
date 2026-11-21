<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCredencialRequest;
use App\Http\Requests\UpdateCredencialRequest;
use App\Models\Credencial;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class CredencialController extends Controller
{
    /**
     * Busca una credencial del usuario autenticado por ID.
     * Si no existe, responde con un error 404 en español.
     */
    private function buscarCredencial(Request $request, int $id): Credencial
    {
        $credencial = $request->user()->credenciales()->find($id);

        if (! $credencial) {
            abort(response()->json([
                'message' => "No se encontró ninguna credencial con el ID #{$id} en tu bóveda.",
            ], 404));
        }

        return $credencial;
    }

    /**
     * Listar todas las credenciales activas del usuario autenticado.
     * Permite búsqueda opcional con ?buscar=... e incluye el total de cambios en el historial.
     */
    public function index(Request $request): JsonResponse
    {
        $query = $request->user()->credenciales()->withCount('historial')->latest();

        if ($request->filled('buscar')) {
            $termino = $request->input('buscar');
            $query->where(function ($q) use ($termino) {
                $q->where('nombre_aplicacion', 'ilike', "%{$termino}%")
                  ->orWhere('correo_o_usuario', 'ilike', "%{$termino}%");
            });
        }

        $credenciales = $query->get();

        return response()->json([
            'data' => $credenciales,
        ], 200);
    }

    /**
     * Guardar una nueva credencial en la bóveda del usuario.
     */
    public function store(StoreCredencialRequest $request): JsonResponse
    {
        $credencial = $request->user()->credenciales()->create(
            $request->validated()
        );

        return response()->json([
            'message'    => 'Credencial guardada exitosamente.',
            'credencial' => $credencial,
        ], 201);
    }

    /**
     * Mostrar los detalles de una credencial específica junto con su historial.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $credencial = $this->buscarCredencial($request, $id);
        $credencial->load('historial');
        $credencial->loadCount('historial');

        return response()->json([
            'credencial' => $credencial,
        ], 200);
    }

    /**
     * Actualizar una credencial existente.
     * Si la contraseña cambia, se registra el movimiento en credencial_historial con el motivo.
     */
    public function update(UpdateCredencialRequest $request, int $id): JsonResponse
    {
        $credencial = $this->buscarCredencial($request, $id);

        DB::transaction(function () use ($request, $credencial) {
            // Registrar en historial solo si la contraseña realmente cambió
            if ($request->filled('contrasena') && $request->contrasena !== $credencial->contrasena) {
                $credencial->historial()->create([
                    'contrasena_anterior' => $credencial->contrasena,
                    'contrasena_nueva'    => $request->contrasena,
                    'motivo'              => $request->input('motivo'),
                ]);
            }

            $credencial->update($request->validated());
        });

        $credencial->loadCount('historial');

        return response()->json([
            'message'    => 'Credencial actualizada correctamente.',
            'credencial' => $credencial,
        ], 200);
    }

    /**
     * Eliminar una credencial de la bóveda (elimina su historial en cascada automáticamente).
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $credencial = $this->buscarCredencial($request, $id);
        $credencial->delete();

        return response()->json([
            'message' => "La credencial #{$id} y su historial fueron eliminados correctamente.",
        ], 200);
    }

    /**
     * Consultar la bitácora completa de cambios de contraseña de una credencial.
     */
    public function historial(Request $request, int $id): JsonResponse
    {
        $credencial = $this->buscarCredencial($request, $id);

        return response()->json([
            'credencial_id'     => $credencial->id,
            'nombre_aplicacion' => $credencial->nombre_aplicacion,
            'correo_o_usuario'  => $credencial->correo_o_usuario,
            'total_cambios'     => $credencial->historial()->count(),
            'historial'         => $credencial->historial()->get(),
        ], 200);
    }

    /**
     * Exportar todas las credenciales del usuario como PDF descargable.
     */
    public function exportarPdf(Request $request): Response
    {
        $credenciales = $request->user()->credenciales()->latest()->get();
        $usuario      = $request->user();

        $pdf = Pdf::loadView('pdf.reporte-credenciales', compact('credenciales', 'usuario'))
                  ->setPaper('a4', 'portrait');

        $nombreArchivo = 'KEY-NEXUS-Reporte-' . now()->format('d-m-Y') . '.pdf';

        return $pdf->download($nombreArchivo);
    }
}
