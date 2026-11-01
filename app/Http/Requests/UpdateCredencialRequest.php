<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCredencialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'nombre_aplicacion' => 'sometimes|required|string|max:150',
            'correo_o_usuario'  => 'sometimes|required|string|max:150',
            'contrasena'        => 'sometimes|required|string',
            'url'               => 'nullable|url|max:255',
            'notas'             => 'nullable|string|max:1000',
        ];

        // Obtener la credencial actual para comparar si la contraseña realmente cambió
        $id = $this->route('credenciale') ?? $this->route('id');
        $credencial = $id ? $this->user()?->credenciales()->find($id) : null;

        // Solo exigimos 'motivo' si se envió 'contrasena' Y es DISTINTA a la actual en la BD
        if ($this->filled('contrasena') && $credencial && $this->contrasena !== $credencial->contrasena) {
            $rules['motivo'] = 'required|string|max:500';
        } else {
            $rules['motivo'] = 'nullable|string|max:500';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'nombre_aplicacion.required' => 'El nombre de la aplicación no puede estar vacío.',
            'nombre_aplicacion.string'   => 'El nombre de la aplicación debe ser un texto.',
            'nombre_aplicacion.max'      => 'El nombre de la aplicación no debe superar los 150 caracteres.',

            'correo_o_usuario.required'  => 'El correo o usuario no puede estar vacío.',
            'correo_o_usuario.string'    => 'El correo o usuario debe ser un texto.',
            'correo_o_usuario.max'       => 'El correo o usuario no debe superar los 150 caracteres.',

            'contrasena.required'        => 'La contraseña no puede estar vacía.',
            'contrasena.string'          => 'La contraseña debe ser una cadena de texto.',

            'motivo.required'            => 'Debes indicar el motivo por el cual estás cambiando la contraseña.',
            'motivo.string'              => 'El motivo debe ser una cadena de texto.',
            'motivo.max'                 => 'El motivo no debe superar los 500 caracteres.',

            'url.url'                    => 'Introduce una dirección URL válida (ej. https://ejemplo.com).',
            'url.max'                    => 'La dirección URL no debe superar los 255 caracteres.',

            'notas.string'               => 'Las notas deben ser una cadena de texto.',
            'notas.max'                  => 'Las notas no deben superar los 1000 caracteres.',
        ];
    }
}
