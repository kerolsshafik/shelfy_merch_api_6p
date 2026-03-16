<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posm extends Model
{
    use HasFactory;

    protected $fillable = ['visit_id', 'store_id', 'store_type'];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function images()
    {
        return $this->hasMany(PosmImage::class, 'pos_m_id');
    }
}
