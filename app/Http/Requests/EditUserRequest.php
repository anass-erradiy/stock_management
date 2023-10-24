<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EditUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true ;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'userName' => [
                Rule::unique('users', 'userName')->ignore(Auth::user()->id)
                ],
            'email' => [
                'email',
                Rule::unique('users', 'email')->ignore(Auth::user()->id)
            ],
            'phoneNumber' => [
                'numeric',
                Rule::unique('users','phoneNUmber')->ignore(Auth::user()->id)
                ] ,
            'role' => 'in:seller,buyer,admin'
        ];
    }
}
