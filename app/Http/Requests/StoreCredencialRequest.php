<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCredencialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_aplicacion' => 'required|string|max:150',
            'correo_o_usuario'  => 'required|string|max:150',
            'contrasena'        => 'required|string',
            'url'               => 'nullable|url|max:255',
            'notas'             => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_aplicacion.required' => 'El nombre de la aplicación o sitio es obligatorio.',
            'nombre_aplicacion.string'   => 'El nombre de la aplicación debe ser un texto.',
            'nombre_aplicacion.max'      => 'El nombre de la aplicación no debe superar los 150 caracteres.',

            'correo_o_usuario.required'  => 'El correo o nombre de usuario es obligatorio.',
            'correo_o_usuario.string'    => 'El correo o usuario debe ser un texto.',
            'correo_o_usuario.max'       => 'El correo o usuario no debe superar los 150 caracteres.',

            'contrasena.required'        => 'La contraseña es obligatoria.',
            'contrasena.string'          => 'La contraseña debe ser una cadena de texto.',

            'url.url'                    => 'Introduce una dirección URL válida (ej. https://ejemplo.com).',
            'url.max'                    => 'La dirección URL no debe superar los 255 caracteres.',

            'notas.string'               => 'Las notas deben ser una cadena de texto.',
            'notas.max'                  => 'Las notas no deben superar los 1000 caracteres.',
        ];
    }
}
