<?php

namespace App\Http\Requests\Tutorial;

use App\Helpers\ResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTutorialRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Hanya admin yang bisa buat tutorial
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'category_id'    => 'required|integer|exists:categories,id',
            'title'          => 'required|string|max:255',
            'content'        => 'required|string',
            'difficulty'     => 'required|in:beginner,intermediate,advanced',
            'estimated_time' => 'required|integer|min:1|max:1440',
            'youtube_url'    => 'nullable|url|max:500',
            'image_url'      => 'nullable|url|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required'    => 'Kategori wajib diisi.',
            'category_id.exists'      => 'Kategori tidak ditemukan.',
            'title.required'          => 'Judul tutorial wajib diisi.',
            'content.required'        => 'Konten tutorial wajib diisi.',
            'difficulty.required'     => 'Tingkat kesulitan wajib diisi.',
            'difficulty.in'           => 'Tingkat kesulitan harus: beginner, intermediate, atau advanced.',
            'estimated_time.required' => 'Estimasi waktu wajib diisi (dalam menit).',
            'estimated_time.min'      => 'Estimasi waktu minimal 1 menit.',
            'estimated_time.max'      => 'Estimasi waktu maksimal 1440 menit (24 jam).',
            'youtube_url.url'         => 'Format YouTube URL tidak valid.',
            'image_url.url'           => 'Format Image URL tidak valid.',
        ];
    }

    protected function failedAuthorization(): void
    {
        throw new HttpResponseException(
            ResponseHelper::forbidden('Hanya admin yang dapat membuat tutorial.')
        );
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            ResponseHelper::validationError($validator->errors())
        );
    }
}
