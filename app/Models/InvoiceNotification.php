<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceNotification extends Model
{
    use HasFactory;
    protected $connection = 'mysql_without_prefix';
    // protected $table="invoice_notifications";

    protected $table="invoice_notifications";


    public function customer() // Correct spelling
    {
        return $this->belongsTo(Customer::class, 'user_id', 'id');
    }
    
}
