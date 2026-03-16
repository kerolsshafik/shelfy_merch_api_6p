<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    public $table = 'product_categories';

    protected $fillable = ['name_en', 'name_ar', 'image', 'slug', 'parent', 'order', 'category_id'];

    public function products()
    {

        return $this->belongsToMany(\App\Models\Product::class, 'categories_products', 'category_id', 'product_id');
    }

    public function children()
    {
        return $this->hasMany(Self::class, 'parent', 'category_id')->orderBy('order');
    }

    public function productOsa()
    {
        return $this->hasMany(ProductOsa::class, 'category_id', 'parent');
    }

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return 'https://erp.momentum-sol.com/storage/app/public/img/products/' . $this->image;
        } else {
            $this->image;
        }
    }
    public function main_parent()
    {
        return $this->belongsTo(Self::class, 'parent', 'category_id');
    }

    public function invoiceCategories(){
        return $this->hasMany(InvoiceCategory::class, 'category_id', 'category_id');
    }
}
