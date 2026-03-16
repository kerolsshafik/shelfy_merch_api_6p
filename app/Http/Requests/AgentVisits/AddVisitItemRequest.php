<?php

namespace App\Http\Requests\AgentVisits;

use Illuminate\Foundation\Http\FormRequest;

class AddVisitItemRequest extends FormRequest
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
            'visit_id' => ['required', 'integer', 'exists:visits,id'],
            'product_ids' => [
                'required',
                'string',
                'regex:/^\d+(,\d+)*$/',
                function ($attribute, $value, $fail) {
                    $ids = explode(',', $value);

                    $count = \App\Models\Product::whereIn('id', $ids)->count();

                    if ($count !== count($ids)) {
                        $fail("One or more product IDs do not exist.");
                    }
                }
            ],
            'images_before' => ['required', 'array'],
            'images_before.*' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'images_after' => ['required', 'array'],
            'images_after.*' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ];
    }
}
