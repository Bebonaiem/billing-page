<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

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
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\-\.]+$/'],
            'first_name' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z\s\-\.]+$/'],
            'last_name' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z\s\-\.]+$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[+]?[\d\s\-\(\)]+$/'],
            'company' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\s\-\.\&]+$/'],
            'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers()->symbols(), 'confirmed'],
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
            'name.required' => 'The name field is required.',
            'name.regex' => 'The name may only contain letters, spaces, hyphens, and periods.',
            'first_name.regex' => 'The first name may only contain letters, spaces, hyphens, and periods.',
            'last_name.regex' => 'The last name may only contain letters, spaces, hyphens, and periods.',
            'email.required' => 'The email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'phone.regex' => 'The phone number may only contain numbers, spaces, hyphens, and parentheses.',
            'company.regex' => 'The company name may only contain letters, numbers, spaces, hyphens, periods, and ampersands.',
            'password.required' => 'The password is required.',
            'password.min' => 'The password must be at least 8 characters long.',
            'password.confirmed' => 'The password confirmation does not match.',
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
            'name' => trim($this->input('name')),
            'first_name' => trim($this->input('first_name')),
            'last_name' => trim($this->input('last_name')),
            'email' => strtolower(trim($this->input('email'))),
            'phone' => preg_replace('/[^\d\+\(\)]/', '', $this->input('phone')),
            'company' => trim($this->input('company')),
            'password' => $this->input('password'),
        ];
    }

    /**
     * Create a new user from the request data.
     *
     * @return \App\Models\User
     */
    public function createUser(): \App\Models\User
    {
        $data = $this->sanitized();
        
        return \App\Models\User::create([
            'name' => $data['name'],
            'first_name' => $data['first_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'company' => $data['company'] ?? null,
            'password' => Hash::make($data['password']),
            'status' => 'active',
            'language' => 'en',
            'timezone' => 'UTC',
            'currency' => 'USD',
            'marketing_emails' => true,
            'is_admin' => false,
        ]);
    }
}