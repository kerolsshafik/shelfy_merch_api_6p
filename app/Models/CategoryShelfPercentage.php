<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryShelfPercentage extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'store_id',
        'category_id',
        'percentage',
        'is_parent',
    ];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }
}
