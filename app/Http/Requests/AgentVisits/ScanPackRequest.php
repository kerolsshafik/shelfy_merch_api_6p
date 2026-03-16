<?php

namespace App\Http\Requests\Agents;

use Illuminate\Foundation\Http\FormRequest;

class ScanPackRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'barcode' => 'required',
        ];
    }
}

