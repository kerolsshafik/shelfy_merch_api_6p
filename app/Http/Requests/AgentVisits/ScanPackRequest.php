<?php

namespace App\Http\Requests\AgentVisits;
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
            'visit_id' => 'required|exists:visits,id',
            'store_id' => 'required|exists:stores,id',
            'barcode' => 'required|string',
        ];
    }
}
