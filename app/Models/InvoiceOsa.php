<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceOsa extends Model
{
    use HasFactory;
    protected $table = 'invoices_osa';

    protected $fillable = ['product_id','invoice_id','status','note'];
}
