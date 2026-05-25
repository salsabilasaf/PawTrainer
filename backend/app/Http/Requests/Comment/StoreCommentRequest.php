<?php

namespace App\Http\Requests\Comment;

use App\Helpers\ResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Admin dan user bisa comment
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'tutorial_id' => 'required|integer|exists:tutorials,id',
            'comment'     => 'required|string|min:3|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'tutorial_id.required' => 'Tutorial ID wajib diisi.',
            'tutorial_id.exists'   => 'Tutorial tidak ditemukan.',
            'comment.required'     => 'Komentar tidak boleh kosong.',
            'comment.min'          => 'Komentar minimal 3 karakter.',
            'comment.max'          => 'Komentar maksimal 1000 karakter.',
        ];
    }

    protected function failedAuthorization(): void
    {
        throw new HttpResponseException(
            ResponseHelper::unauthorized('Anda harus login untuk mengomentari tutorial.')
        );
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            ResponseHelper::validationError($validator->errors())
        );
    }
}
