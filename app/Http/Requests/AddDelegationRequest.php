<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use RealRashid\SweetAlert\Facades\Alert;

class AddDelegationRequest extends FormRequest
{
    public function rules()
    {
        return [
            'delegate_to_date' => 'date|after_or_equal:today',
            'delegate_from_date' => 'date|after_or_equal:today',
            'delegate_from_emp' => 'required',
        ];
    }
    public function messages()
    {
        return [
            "delegate_to_date.date"=>Alert::warning('WARNING', __('messages.start_date_today')),
            "delegate_from_date.date"=>Alert::warning('WARNING', __('messages.end_date_today'))
        ];
    }
}


