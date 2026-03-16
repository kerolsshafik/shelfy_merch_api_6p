<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OsaVisit extends Model
{
   use HasFactory;
    protected $table = 'osa_visits';
    protected $guarded = [];

    public function visit(){
        return $this->belongsTo(Visit::class, 'visit_id', 'id');
    }

    public function product(){
        return $this->belongsTo(Product::class, 'product_id', 'id_erp');
    }
}
