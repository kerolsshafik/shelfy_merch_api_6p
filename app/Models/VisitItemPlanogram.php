<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitItemPlanogram extends Model
{
    use HasFactory;
    protected $table = 'visit_item_planograms';
    protected $guarded = [];

    public function visitItem()
    {
        return $this->belongsTo(VisitItem::class, 'visit_item_id', 'id');
    }
}
