<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image;
use File;
use Storage;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

use Tymon\JWTAuth\Contracts\JWTSubject;

class Customer extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use SoftDeletes;

    protected $connection = 'mysql';
    public $table = 'customers';
    protected $fillable = [
        'name',
        'email',
        'password',
        'auto_substation',
        'birth_date',
        'active',
        'ins',
        'marital_status',
        'image',
        'phone',
        'active',
        'picture',
        'referral_id',
        'account_no'
    ];
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function invoiceActionNotifications()
    {
        return $this->hasMany(InvoiceActionNotification::class, 'customer_id', 'id');
    }

    public function visitNotifications()
    {
        return $this->hasMany(VisitNotification::class, 'customer_id', 'id');
    }
    public function customerstore()
    {
        return $this->hasMany(CustomerStore::class, 'customer_id', 'id');
    }
}
