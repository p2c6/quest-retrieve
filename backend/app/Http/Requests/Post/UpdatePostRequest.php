<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
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
            'subcategory_id' => 'required|exists:subcategories,id', 
            'type' => 'required|string', 
            'incident_location' => 'required|string',
            'incident_date' => 'required|date', 
        ];
    }

    public function messages(): array
    {
        $messages = ['subcategory_id' => 'The item field is required.'];

        return $messages;
    }
}
