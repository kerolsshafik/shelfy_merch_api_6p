<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOsa extends Model
{
    use HasFactory;

    protected $table = 'products_osa';

    protected $fillable = ['barcode','segment','instructions', 'planogram'];
    protected $casts = [
        'barcode' => 'array',
    ];
}
