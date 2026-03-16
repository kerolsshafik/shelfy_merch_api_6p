<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosMaterial extends Model
{
    use HasFactory;

    protected $fillable = ['visit_id', 'description'];

    public function images()
    {
        return $this->hasMany(MaterialImage::class);
    }
}
