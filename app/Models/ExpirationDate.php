<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpirationDate extends Model
{
    use HasFactory;
    protected $table = 'return_expiration_dates';
    protected $guarded = [];

    public function returnItem()
    {
        return $this->belongsTo(ReturnItem::class, 'return_id', 'id');
    }
}
