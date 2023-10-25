<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule ;
use Spatie\FlareClient\Api;

class CreateProudctRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $userId = $this->route('product') ;
        $method = $this->method() ;
        return [
            'product_name' => [
                Rule::requiredIf($userId),
                'string'
            ] ,
            'quantity' => [
                Rule::requiredIf($userId) ,
                'min:1',
                'integer'
                ] ,
            'description' => 'string' ,
            'price' => [
                Rule::requiredIf($userId)
                ,'numeric'
                ]
        ];
    }
}
