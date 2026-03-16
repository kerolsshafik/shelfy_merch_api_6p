<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerStore extends Model
{
    use HasFactory;

    public function stores()
    {
        return $this->hasMany(Store::class, 'storekey', 'store_id');
    }
}
