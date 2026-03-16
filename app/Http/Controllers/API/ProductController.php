<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Traits\ApiResponseTrait;
use App\Models\ProductVariation;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Support\Facades\App;

class ProductController extends Controller
{
    use ApiResponseTrait;

    public function get_product_barcode(Request $request)
    {
        $rules = [
            'barcode' => 'required',
            // 'category_id' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->errorResponse('validation Error', 422, $validator->errors());
        }

        $barcode = $request->barcode;
        $prod = ProductVariation::where('barcode', $barcode)->first();
        $product_category = Product::where('id', $prod->product_id)->first();
        // dd($product_category);
        $result = get_category_id([$product_category->productcategory_id]);
        $store = Store::where('id', $request->category_id)->first();
        // dd($result);

        $cat=Product::where('products.id', $prod->product_id)
                ->whereIn('productcategory_id', $result)->with([
                    'category.productOsa' => function ($q) use ($store) {
                        $q->where('segment', $store->segment)->select('category_id');
                    }

                ]);

        if (strncmp($barcode, '0', 1) === 0) {
            $barcode = substr($barcode, 1);
        }
        if (str_starts_with($barcode, '21') || str_starts_with($barcode, '20')) {
            $barcode = substr_replace($barcode, '000000', -6);
        }
        $prod = ProductVariation::where('barcode', $barcode)->first();
        if (!is_null($prod)) {
            $data = Product::where('products.id', $prod->product_id)
                ->whereIn('productcategory_id', $result)
                ->with([
                    'category' => function ($q) {
                        $q->with([
                            'main_parent' => function ($q) {
                                $q->with('main_parent');
                            },
                        ]);
                        $q->select('*', 'product_categories.title as category_name');
                    },

                ])
                ->with([
                    'category.productOsa' => function ($q) use ($store) {
                        $q->where('segment', $store->segment)->select('id', 'segment', 'category_id', 'instructions', 'planogram');
                    }

                ])
                ->with([
                    'standard' => function ($q) use ($request) {
                        $q->groupBy('product_variations.barcode')->distinct();
                        $q->select('product_variations.*', 'product_variations.image', 'product_variations.barcode', 'product_variations.price', 'product_variations.discount_flag', 'product_variations.discount_price');
                    },
                    'segements' => function ($q) use ($store) {
                        $q->where('segment_id', $store->segment);
                        // $q->with('segment_name');
                    },
                ])
                ->when(App::getLocale() == 'en', function ($query) {
                    $query->select(['*', 'products.id as id', 'products.name as name', 'product_des as description']);
                })
                ->when(App::getLocale() == 'ar', function ($query) {
                    $query->select(['*', 'products.id as id', 'products.name_ar as name', 'description_ar as description']);
                })
                ->first();

            if (is_null($data)) {
                $message = App::getLocale() == 'en' ? 'Product Not Found' : 'المنتج غير موجود';
                return $this->errorResponse($message, 401, null);
            }
            $message = App::getLocale() == 'en' ? 'Product returned successfully' : 'تمت إعادة المنتج بنجاح';
            return $this->successResponse($data, $message);
        }

        if (is_null($prod)) {
            $message = App::getLocale() == 'en' ? 'Product Not Found' : 'المنتج غير موجود';
            return $this->errorResponse($message, 401, null);
        }
    }
}
