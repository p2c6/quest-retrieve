<?php

namespace App\Http\Requests\Public\Post;

use Illuminate\Foundation\Http\FormRequest;

class ReturnRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'item_description' => 'required|string',
            'where' => 'required|string',
            'when' => 'required|date',
            'message' => 'required|string',
            'full_name' => 'required|string',
        ];
    }

    public function messages(): array
    {
        $messages = [
            'full_name' => 'The name is required.',
            'item_description' => 'The description is required.',
        ];

        return $messages;
    }
}
