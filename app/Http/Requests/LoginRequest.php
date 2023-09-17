<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use RealRashid\SweetAlert\Facades\Alert;

class LoginRequest extends FormRequest
{
    public function rules()
    {
        return [
            'employee_number' => ['required','integer']
        ];
    }
    public function messages()
    {
        return [
            "employee_number.required"=>Alert::warning('WARNING', __('messages.login_employee_number_integer'))
        ];
    }
}


