<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'content' => 'required|string|min:1|max:280',
            'platforms' => 'required|array|min:1',
            'platforms.*' => 'string|in:twitter,reddit',
            'scheduled_date' => 'nullable|date_format:Y-m-d H:i:s',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'content.required' => 'El contenido de la publicación es obligatorio.',
            'content.min' => 'El contenido de la publicación no puede estar vacío.',
            'content.max' => 'El contenido de la publicación no puede exceder los 280 caracteres.',
            'platforms.required' => 'Debes seleccionar al menos una plataforma.',
            'platforms.min' => 'Debes seleccionar al menos una plataforma.',
            'platforms.*.in' => 'Una de las plataformas seleccionadas no es válida.',
        ];
    }
}
