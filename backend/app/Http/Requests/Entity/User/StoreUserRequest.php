<?php

namespace App\Http\Requests\Entity\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'password' => 'required|confirmed|max:16',
            'role_id' => 'required'
        ];
    }

    public function messages(): array
    {
        $messages = ['role_id' => 'The role field is required.'];

        return $messages;
    }
}
