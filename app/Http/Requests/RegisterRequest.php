<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'userName' => 'required' ,
            'email' => 'required|unique:users,email',
            'role' => 'required|in:seller,buyer,admin',
            'phoneNumber' => 'string|unique:users,phoneNumber' ,
            'password' => 'required|min:8'
        ];
    }
}
