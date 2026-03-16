<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Symfony\Component\Mime\Part\Multipart\AlternativePart;



class Product extends Model
{
  //
  protected $table = 'products';


  //   protected $appends = ['liked', 'cart', 'unite' , 'notified'];

  protected $casts = [
    'id_erp' => 'integer',
  ];
  public function offer()
  {
    return $this->hasMany(ProductOffer::class, 'product_id');
  }
  public function standard()
  {
    return $this->hasOne(ProductVariation::class);
  }
  public function branches()
  {
    return $this->belongsToMany(\App\Models\Company::class, 'product_branch', 'product_id', 'branch_id');
  }
  public function category()
  {
    return $this->hasOne(Category::class, 'category_id', 'productcategory_id');
  }

  public function cuts()
  {
    return $this->hasMany(Cuts::class, 'product_id', 'id_erp');
  }
  public function list()
  {
    return $this->belongsToMany(ProductList::class, 'lists_products', 'product_id', 'list_id', 'id_erp');
  }
  public function getUniteAttribute()
  {
    return strtolower($this->unit);
  }
  public function lists()
  {
    return $this->belongsToMany(ProductList::class, 'lists_products', 'list_id', 'product_id');
  }
  public function getLikedAttribute()
  {
    if ((auth('api')->user())) {
      $wishlistId = DB::table('wishlist')->where('user_id', auth('api')->user()->id)->pluck('product_id')->toArray();
      if (in_array($this->id_erp, $wishlistId)) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  public function getNotifiedAttribute()
  {
    if ((auth('api')->user())) {
      $notifiId = DB::table('products_notify')->where('user_id', auth('api')->user()->id)->pluck('barcode')->toArray();
      $ids = ProductVariation::where('product_id', $this->id)->first();
      if (in_array($ids->barcode, $notifiId)) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }


  //   public function getCartAttribute()
//   {
//     if ((auth('api')->user())) {
//       $cartIds = Cart::where('user_id', auth('api')->user()->id)->pluck('product_id')->toArray();
//       if (in_array($this->id_erp, $cartIds)) {
//         $qty = Cart::where('user_id', auth('api')->user()->id)->where('product_id', $this->id_erp)->first();
//         return $qty->qty;
//       } else {
//         return 0;
//       }
//     } else {
//       return 0;
//     }
//   }

  public function alternativs()
  {
    return $this->hasMany(AlternativeProducts::class, 'alternative_product_id', 'id_erp');
  }

  public function segements()
  {
    return $this->hasMany(SelfImagesProductSegment::class, 'id_erp', 'id_erp');
  }
  //   public function alternativs(){
//     return $this->belongsToMany(AutoSubstitution::class, 'auto_substitution_products_alternative', 'auto_substitution_id', 'product_id','id_erp');
// }
//   protected static function boot()
//   {
//       parent::boot();

  //               static::addGlobalScope('master', function($builder){
//               $builder->where('master', '=', 0);
//              });

  //   }
}
