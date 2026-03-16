<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelfImagesProductSegment extends Model
{
    use HasFactory;
    protected $table = 'self_images_product_segment';

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    public function segment_name()
    {

        return $this->belongsTo(Segmentation::class,'segment_id','id');
    }
}
