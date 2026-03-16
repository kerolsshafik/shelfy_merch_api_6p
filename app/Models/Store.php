<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'main',
        'rel_id',
        'employee_id',
        'name',
        'phone',
        'address',
        'city',
        'region',
        'country',
        'postbox',
        'email',
        'picture',
        'company',
        'taxid',
        'name_s',
        'phone_s',
        'email_s',
        'address_s',
        'city_s',
        'region_s',
        'country_s',
        'postbox_s',
        'balance',
        'docid',
        'custom1',
        'ins',
        'active',
        'password',
        'role_id',
        'remember_token',
        'referral_id',
        'account_no',
        'provider',
        'provider_id',
        'birth_date',
        'auto_substation',
        'store_name',
        'segment',
        'governorate',
        'lat',
        'lng',
        'store_status',
        'points'
    ];

    protected $dates = ['deleted_at'];
}
