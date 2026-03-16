<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnItem extends Model
{
    use HasFactory;
    protected $table = 'returnes';
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id_erp');
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class, 'visit_id', 'id');
    }

    public function expirationDates()
    {
        return $this->hasMany(ExpirationDate::class, 'return_id', 'id');
    }
}
