<?php

use App\Models\Cart;
use PhpSpellcheck\Spellchecker\Aspell;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use App\Models\Category;



function get_category_id($categories)
{
    $result = [];
    $total = [];
    $categoryIds=[];
    $subChildren=[];
    foreach ($categories as $category) {
        $category = Category::where('category_id', $category)->first();

        if(isset($category->children)){
            $categoryIds = $category->children->pluck('category_id')->toArray();
            $subChildren = Category::WhereIn('parent', $categoryIds)->pluck('category_id')->toArray();
            $lastChildern=Category::WhereIn('parent', $subChildren)->pluck('category_id')->toArray();
        }


        array_push($total, $category->category_id);
        $total = array_merge($total, $categoryIds, $subChildren,$lastChildern);
        $total = array_unique($total);
        $result = $total;
    }
    return $result;
}

