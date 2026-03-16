<?php

namespace App\Http\Requests\AgentVisits;

use Illuminate\Foundation\Http\FormRequest;

class RemoveReturnRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'visit_id' => 'required|exists:visits,id',
            "product_id" => 'required|exists:products,id',
        ];
    }
}
