<?php

namespace App\Http\Requests\AgentVisits;

use Illuminate\Foundation\Http\FormRequest;

class StoreVisitProductPriceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'visit_id' => ['required', 'integer', 'exists:mysql.rose_visits,id'],
            'store_id' => ['required', 'integer', 'exists:mysql.rose_stores,id'],
            'barcode' => ['required', 'integer'],
            'price' => ['required', 'numeric', 'min:0'],
        ];
    }
}

