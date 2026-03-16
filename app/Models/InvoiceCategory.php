<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceCategory extends Model
{
    use HasFactory;
    protected $table = 'invoice_categories';
    protected $guarded = [];
    public function invoiceCategoryImages()
    {
        return $this->hasMany(InvoiceCategoryImage::class, 'invoice_category_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function invoice()
    {
        return $this->belongsTo(InvocieShelfy::class, 'invoice_id', 'id');
    }
}
