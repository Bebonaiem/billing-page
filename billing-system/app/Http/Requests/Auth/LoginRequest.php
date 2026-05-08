<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class LoginRequest extends FormRequest
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
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'The email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'password.required' => 'The password is required.',
        ];
    }

    /**
     * Get the sanitized input data.
     *
     * @return array<string, mixed>
     */
    public function sanitized(): array
    {
        return [
            'email' => strtolower(trim($this->input('email'))),
            'password' => $this->input('password'),
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @return bool
     */
    public function authenticate(): bool
    {
        $credentials = $this->sanitized();
        
        return Auth::attempt($credentials, $this->boolean('remember'));
    }
}