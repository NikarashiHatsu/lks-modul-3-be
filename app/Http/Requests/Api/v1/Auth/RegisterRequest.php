<?php

namespace App\Http\Requests\Api\v1\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'full_name' => ['required', 'string'],
            'bio' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'unique:users,username', 'min:3', 'regex:/^[a-zA-Z0-9._]+$/'],
            'password' => ['required', 'string', 'confirmed'],
            'is_private' => ['required', 'boolean'],
        ];
    }
}
