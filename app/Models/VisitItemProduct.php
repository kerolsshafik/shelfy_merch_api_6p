<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitItemProduct extends Model
{
    use HasFactory;
    protected $table = 'visit_item_product';
    protected $guarded = [];

    public function visitItem()
    {
        return $this->belongsTo(VisitItem::class, 'visit_item_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id_erp');
    }
}
