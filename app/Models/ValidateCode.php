<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ValidateCode extends Model
{
    public $table = 'validate_code';
    protected $fillable = ['code', 'phone'];


}
