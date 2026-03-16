<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosmImage extends Model
{
    use HasFactory;

    protected $fillable = ['pos_m_id', 'image_path'];

    public function posm()
    {
        return $this->belongsTo(Posm::class, 'pos_m_id');
    }
}
