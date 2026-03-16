<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\InvocieShelfy;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductOsa;
use App\Models\ProductVariation;
use App\Models\InvocieProduct;
use App\Models\InvoiceOsa;
use App\Models\Store;
use App\Models\Visit;
use App\Models\VisitItem;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;



class OsaController extends Controller
{

    use ApiResponseTrait;

    // new
    public function get_osa($id)
    {
        $invoice = InvocieShelfy::find($id);
        $store = Store::where('id', $invoice->category_id)->first();
        $id_erp = InvocieProduct::where('invoice_id', $id)->pluck('id_erp')->ToArray();

        $products = Product::whereIn('id_erp', $id_erp)->pluck('id')->ToArray();


        $categoryProducts = Product::whereIn('id_erp', $id_erp)->pluck('productcategory_id')->ToArray();
        $parents = Category::whereIn('category_id', $categoryProducts)->pluck('parent')->ToArray();

        $barcodes = ProductVariation::whereIn('product_id', $products)->pluck('barcode')->ToArray();
        /// products invoice
        $osa_products = ProductOsa::
            //   where(function ($query) use ($barcodes) {
            //     foreach ($barcodes as $barcode) {
            //         $query->orWhereJsonContains('barcode', $barcode);
            //     }
            // })
            where('segment', $store->segment)
            ->whereIn('category_id', $parents)
            ->pluck('barcode')
            // ->pluck('category_id')
            ->toArray();
        $flattenedArray = array_merge(...$osa_products);

        // dd($osa_products);
        if (empty($osa_products) || empty($barcodes)) {
            $message = App::getLocale() == 'en' ? 'No osa products found' : 'no osa products found';
            return $this->successResponse([], $message);
        }

        $product_barcodes_new = ProductVariation::whereIn('barcode', $flattenedArray)->whereNotIn('barcode', $barcodes)->pluck('product_id')->ToArray();
        $products = Product::with([
            'segements' => function ($q) use ($store) {
                $q->where('segment_id', $store->segment);
            },
            'standard' => function ($q) {
                $q->select('product_id', 'image', 'barcode');
            },
        ])->when(App::getLocale() == 'en', function ($query) {
            $query->select(['id_erp', 'products.id as id', 'products.id as id', 'products.name as name', 'product_des as description']);
        })
            ->when(App::getLocale() == 'ar', function ($query) {
                $query->select(['id_erp', 'products.id as id', 'products.name_ar as name', 'description_ar as description']);
            })
            // ->whereIn('productcategory_id', $categoryProducts)->get();
            ->whereIn('id', $product_barcodes_new)->get();

        $message = App::getLocale() == 'en' ? 'osa products returned Successfully' : 'osa products returned Successfully';
        return $this->successResponse($products, $message);
    }

    public function getOsaV2($id)
    {
        // $invoice = InvocieShelfy::find($id);
        $visit = Visit::find($id);
        $store = Store::where('id', $visit->store_id)->first();
        // $id_erp = InvocieProduct::where('invoice_id', $id)->pluck('id_erp')->ToArray();
        $id_erp = VisitItem::with('VisitItemProducts.product')->where('visit_id', $visit->id)->get()->pluck('VisitItemProducts.*.product.id_erp')->flatten()->filter()->values()->toArray();
        // $id_erp = VisitItem::with('VisitItemProducts.product')->where('visit_id', $visit->id)->get();

        // dd($id_erp);

        $products = Product::whereIn('id_erp', $id_erp)->pluck('id')->ToArray();

        // dd($products);

        $categoryProducts = Product::whereIn('id_erp', $id_erp)->pluck('productcategory_id')->ToArray();
        $parents = Category::whereIn('category_id', $categoryProducts)->pluck('parent')->ToArray();

        $barcodes = ProductVariation::whereIn('product_id', $products)->pluck('barcode')->ToArray();
        /// products invoice
        $osa_products = ProductOsa::
            //   where(function ($query) use ($barcodes) {
            //     foreach ($barcodes as $barcode) {
            //         $query->orWhereJsonContains('barcode', $barcode);
            //     }
            // })
            where('segment', $store->segment)
            ->whereIn('category_id', $parents)
            ->pluck('barcode')
            // ->pluck('category_id')
            ->toArray();
        $flattenedArray = array_merge(...$osa_products);

        // dd($osa_products);
        if (empty($osa_products) || empty($barcodes)) {
            $message = App::getLocale() == 'en' ? 'No osa products found' : 'no osa products found';
            return $this->successResponse([], $message);
        }

        $product_barcodes_new = ProductVariation::whereIn('barcode', $flattenedArray)->whereNotIn('barcode', $barcodes)->pluck('product_id')->ToArray();
        $products = Product::with([
            'segements' => function ($q) use ($store) {
                $q->where('segment_id', $store->segment);
            },
            'standard' => function ($q) {
                $q->select('product_id', 'image', 'barcode');
            },
        ])->when(App::getLocale() == 'en', function ($query) {
            $query->select(['id_erp', 'products.id as id', 'products.id as id', 'products.name as name', 'product_des as description']);
        })
            ->when(App::getLocale() == 'ar', function ($query) {
                $query->select(['id_erp', 'products.id as id', 'products.name_ar as name', 'description_ar as description']);
            })
            // ->whereIn('productcategory_id', $categoryProducts)->get();
            ->whereIn('id', $product_barcodes_new)->get();

        $message = App::getLocale() == 'en' ? 'osa products returned Successfully' : 'osa products returned Successfully';
        return $this->successResponse($products, $message);
    }

    // old
    // public function get_osa($id){
    //     $id_erp=InvocieProduct::where('invoice_id',$id)->pluck('id_erp')->ToArray();
    //     $products=Product::whereIn('id_erp', $id_erp)->pluck('id')->ToArray();
    //     $barcodes=ProductVariation::whereIn('product_id', $products)->pluck('barcode')->ToArray();
    //     $osa_products = ProductOsa::where(function ($query) use ($barcodes) {
    //         foreach ($barcodes as $barcode) {
    //             $query->orWhereJsonContains('barcode', $barcode);
    //         }
    //     })
    //     ->where('segment', Auth::user()->segment)
    //     ->pluck('barcode')
    //     ->toArray();
    //     $flattenedArray = array_merge(...$osa_products);

    //     // dd($osa_products);
    //      if(empty($osa_products) || empty($barcodes)){
    //          $message = App::getLocale() == 'en' ? 'No osa products found' : 'no osa products found';
    //          return $this->successResponse([],$message );
    //      }

    //      $product_barcodes_new=ProductVariation::whereIn('barcode', $flattenedArray)->whereNotIn('barcode', $barcodes)->pluck('product_id')->ToArray();
    //      $products=Product::with([
    //         'segements'=>function($q){
    //             $q->where('segment_id',Auth::user()->segment);
    //         },
    //         'standard' => function ($q) {
    //             $q->select('product_id','image', 'barcode');
    //         },
    //     ])->when(App::getLocale() == 'en', function ($query) {
    //         $query->select(['id_erp','products.id as id','products.id as id', 'products.name as name', 'product_des as description']);
    //     })
    //     ->when(App::getLocale() == 'ar', function ($query) {
    //         $query->select(['id_erp','products.id as id', 'products.name_ar as name', 'description_ar as description']);
    //     })
    //     ->whereIn('id', $product_barcodes_new)->get();

    //     $message = App::getLocale() == 'en' ? 'osa products returned Successfully' : 'osa products returned Successfully';
    //     return $this->successResponse($products,$message );
    // }

    public function add_osa(Request $request)
    {
        $rules = [
            'product_id' => 'required',
            'invoice_id' => 'required',
            'status' => 'required',
            'note' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->errorResponse('validation Error', 422, $validator->errors());
        }
        $product = product::where('id', $request->product_id)->first();
        if (!is_null($product)) {
            $invoiceOsa = InvoiceOsa::create([
                'product_id' => $product->id_erp,
                'invoice_id' => $request->invoice_id,
                'status' => $request->status,
                'note' => $request->note,
            ]);

            $message = App::getLocale() == 'en' ? 'invoice Osa stored successfully' : 'invoice Osa stored successfully';
            return $this->successResponse($invoiceOsa, $message, 200);
        } else {
            $message = App::getLocale() == 'en' ? 'no product found' : 'no product found';
            return $this->successResponse([], $message, 200);
        }
    }
}
