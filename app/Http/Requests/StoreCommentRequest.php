<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'author_name' => ['required', 'string', 'min:2', 'max:100'],
            'body' => ['required', 'string', 'min:2', 'max:2000'],
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
            'author_name.required' => 'Enter the comment author name.',
            'author_name.min' => 'Author names should be at least 2 characters.',
            'body.required' => 'Enter a comment body.',
            'body.min' => 'Comments should be at least 2 characters.',
        ];
    }
}
