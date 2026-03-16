<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitProductPrice extends Model
{
    use HasFactory;
    protected $table = 'visit_product_prices';

    public function visit()
    {
        return $this->belongsTo(Visit::class, 'visit_id', 'id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}

