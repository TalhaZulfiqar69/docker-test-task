<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRegistrationRequest extends FormRequest
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
    public function rules(): array{
        return [
            'email' => 'required|email|unique:users',
            'name' => 'required',
            'password' => 'required|min:6|max:10|confirmed',
            'password_confirmation' => 'required|min:6',
        ];
    }

    public function messages() {
        return [
            'email.unique' => 'User already exists with this email.',
            'name.required' => 'Name is required.',
            'password.min' => 'Password must be at least 6 characters',
            'password.max' => 'Password cannot be more than 10 characters',
            'password.confirmed' => 'Password confirmation does not match',
            'password_confirmation.required' => 'Password confirmation is required',
        ];
    }
}
