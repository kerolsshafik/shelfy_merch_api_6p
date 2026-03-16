<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvocieShelfy extends Model
{
  use HasFactory;
  protected $table = 'invocies_shelfy';

  protected $fillable = [
    'invoice_id',
    'category_id',
    'customer_id',
    'points',
    'status',
    'amount',
    'agentid',
  ];
  // protected $hidden = [
  //     'created_at',
  //     'updated_at',
  // ];

  public function images()
  {
    return $this->hasMany(InvoiceImage::class, 'invoice_id', 'id');
  }
  public function products()
  {
    return $this->hasMany(InvocieProduct::class, 'invoice_id', 'id');
  }

  public function invoiceCategory()
  {
    return $this->hasMany(InvoiceCategory::class, 'invoice_id', 'id');
  }
}
