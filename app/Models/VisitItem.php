<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitItem extends Model
{
    use HasFactory;
    protected $table = 'visit_items';
    protected $guarded = [];

    protected $casts = [
        'product_ids' => 'array'
    ];

    public function VisitItemPlanograms()
    {
        return $this->hasMany(VisitItemPlanogram::class, 'visit_item_id', 'id');
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class, 'visit_id', 'id');
    }

    public function VisitItemProducts()
    {
        return $this->hasMany(VisitItemProduct::class, 'visit_item_id', 'id');
    }
}
