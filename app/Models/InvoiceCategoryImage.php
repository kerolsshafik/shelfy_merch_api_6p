<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceCategoryImage extends Model
{
    use HasFactory;
    protected $table = 'invoice_category_image';
    protected $guarded = [];

    public function invoiceCategory()
    {
        return $this->belongsTo(InvoiceCategory::class, 'invoice_category_id');
    }
}
