<?php

namespace App\Http\Requests\Favorite;

use App\Helpers\ResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreFavoriteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'tutorial_id' => 'required|integer|exists:tutorials,id',
        ];
    }

    public function messages(): array
    {
        return [
            'tutorial_id.required' => 'Tutorial ID wajib diisi.',
            'tutorial_id.exists'   => 'Tutorial tidak ditemukan.',
        ];
    }

    protected function failedAuthorization(): void
    {
        throw new HttpResponseException(
            ResponseHelper::unauthorized('Anda harus login untuk menyimpan favorit.')
        );
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            ResponseHelper::validationError($validator->errors())
        );
    }
}
