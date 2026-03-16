<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\product\Traits\ProductVariationRelationship;

class ProductVariation extends Model
{

    protected $table = 'product_variations';

    /**
     * Mass Assignable fields of model
     * @var array
     */
    protected $fillable = [
        'qty', 'product_id'
    ];

    protected $casts = [

        'price' => 'float',
        'discount_price' => 'float',
    ];



    public function getImageUrlAttribute()
    {

        if ($this->image) {
            return 'https://erp.momentum-sol.com/storage/app/public/img/products/' . $this->image;
        } else {
            $this->image;
        }
    }
}
