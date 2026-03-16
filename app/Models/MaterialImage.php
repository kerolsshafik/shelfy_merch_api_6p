<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialImage extends Model
{
    use HasFactory;
    protected $fillable = ['pos_material_id', 'image_path'];

    public function posMaterial()
    {
        return $this->belongsTo(PosMaterial::class);
    }
}
