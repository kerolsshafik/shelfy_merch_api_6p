<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvocieProduct extends Model
{
    use HasFactory;
    protected $table = 'invoice_products';

    protected $fillable = [
        'id_erp',
        'invoice_id',
        'image',
    ];

    protected $hidden = [
      'created_at',
      'updated_at',
  ];

    public function product()
    {
      return $this->hasMany(Product::class,'id_erp','id_erp');
    }
}
