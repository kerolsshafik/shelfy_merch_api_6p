<?php

namespace App\Http\Requests\PosMaterial;

use Illuminate\Foundation\Http\FormRequest;

class PosmStoreImagesRequest extends FormRequest
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
            'store_type' => 'required|in:0,1,2', //  0  for static , 1  for dynamic , 2 both
            'images' => 'required|array|min:1',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}
