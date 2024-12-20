<?php

namespace App\Http\Requests\Entity\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->route('user')->id)
            ],
            'password' =>  'required|confirmed|max:16',
            'user_id' => 'required_if_accepted:password',
            'last_name' => 'required',
            'first_name'  => 'required',
            'birthday' => 'required|date',
            'contact_no' => 'required',
            'role_id' => 'required'
        ];
    }

    public function messages(): array
    {
        $messages = ['role_id' => 'The role field is required.'];

        return $messages;
    }
}
