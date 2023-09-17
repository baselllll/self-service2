<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use RealRashid\SweetAlert\Facades\Alert;

class AddServiceValidRequest extends FormRequest
{
    public function rules()
    {
        return [
            'start_date' => 'date|after_or_equal:today',
            'end_date' => 'date|after_or_equal:today'

        ];
    }
    public function messages()
    {
        return [
            "start_date.after_or_equal" => Alert::warning('WARNING', __('messages.start_date_today')),
            "end_date.after_or_equal" => Alert::warning('WARNING', __('messages.end_date_today'))
        ];
    }
}


