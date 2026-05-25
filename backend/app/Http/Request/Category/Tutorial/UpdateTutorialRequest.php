<?php

namespace App\Http\Requests\Tutorial;

use App\Helpers\ResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTutorialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'category_id'    => 'sometimes|integer|exists:categories,id',
            'title'          => 'sometimes|string|max:255',
            'content'        => 'sometimes|string',
            'difficulty'     => 'sometimes|in:beginner,intermediate,advanced',
            'estimated_time' => 'sometimes|integer|min:1|max:1440',
            'youtube_url'    => 'nullable|url|max:500',
            'image_url'      => 'nullable|url|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.exists'  => 'Kategori tidak ditemukan.',
            'difficulty.in'       => 'Tingkat kesulitan harus: beginner, intermediate, atau advanced.',
            'estimated_time.min'  => 'Estimasi waktu minimal 1 menit.',
            'estimated_time.max'  => 'Estimasi waktu maksimal 1440 menit.',
            'youtube_url.url'     => 'Format YouTube URL tidak valid.',
            'image_url.url'       => 'Format Image URL tidak valid.',
        ];
    }

    protected function failedAuthorization(): void
    {
        throw new HttpResponseException(
            ResponseHelper::forbidden('Hanya admin yang dapat mengubah tutorial.')
        );
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            ResponseHelper::validationError($validator->errors())
        );
    }
}
