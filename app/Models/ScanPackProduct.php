<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScanPackProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'store_id',
        'product_id',
        'product_variation_id',
        'barcode',
    ];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variation()
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id');
    }
}
