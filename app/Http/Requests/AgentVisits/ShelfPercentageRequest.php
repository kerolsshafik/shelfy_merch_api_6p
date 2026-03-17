<?php

namespace App\Http\Requests\AgentVisits;

use Illuminate\Foundation\Http\FormRequest;

class ShelfPercentageRequest extends FormRequest
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
            'categories' => 'required|array|min:1',
            'categories.*.category_id' => 'required|exists:product_categories,category_id',
            'categories.*.percentage' => 'required|numeric|min:0|max:100',
        ];
    }
}
