<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceImage extends Model
{
    use HasFactory;
    protected $table='invoice_images';

    protected $fillable = [
        'invoice_id',
        'image',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

}
