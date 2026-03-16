<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Product;
use App\Models\CategoryProducts;
use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Database;
use Kreait\Firebase\Http\Auth\CustomToken;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Cuts;
use App\Models\FlagsTimes;
use App\Models\Invoice;
use App\Models\InvoiceProducts;
use App\Models\OrderProduct;
use App\Models\OrderProductUpdate;
use App\Models\OrderSlots;
use App\Models\OrderStatus;
use App\Models\Ota;
use App\Models\ProductCut;
use App\Models\ProductVariation;
use App\Models\ValidateCode;
use App\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiResponseTrait;

use Validator;

use function PHPUnit\Framework\isNull;

class CodeController extends Controller
{
    use ApiResponseTrait;

    // ------------------------------------------ send code-----------------------------------------------------------------
    public function send_code(Request $request)
    {
        $rules=[
            'phone' => 'required',
        ];

        $check = Customer::where('phone', $request->phone)->first();

        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return $this->errorResponse('validation Error', 422,$validator->errors());
        }
        $customer = Customer::where('phone', $request->phone)->first();
        if (!$customer) {
            $message = App::getLocale() == 'en' ? 'Customer not found' : 'العميل غير موجود';
            return $this->errorResponse($message, 404);
        }


        else
        {
            if($customer->store_status==0)
            {
                $message = App::getLocale() == 'en' ? 'Customer not active' : 'العميل غير نشط';
                return $this->errorResponse($message, 404);
            }
        }
        $code = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // dd('1');
        ValidateCode::create(['phone'=>$request->phone,'code'=>'111111']);

        $message = App::getLocale() == 'en' ? 'Code  Send Successfully' : 'تم ارسال الكود بنجاح';
        return $this->successResponse([], $message);
    }

    // --------------------------------------------- validate code-----------------------------------------------------------------
    public function validate_code(Request $request)
    {
        $rules=[
            'phone' => 'required',
            'code' => 'required',
        ];
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return $this->errorResponse('validation Error', 422,$validator->errors());
        }

        $check= ValidateCode::where('code',$request->code)->where('phone',$request->phone)->first();
        if(is_null( $check))
        {
            $message = App::getLocale() == 'en' ? 'Code Not Valid' : 'الرمز غير صالح';
            return $this->errorResponse($message, 401);
        }else{
            ValidateCode::where('code',$request->code)->where('phone',$request->phone)->delete();
            $message = App::getLocale() == 'en' ? 'Code Valid' : 'الكود صالح';
            return $this->successResponse([], $message);
        }
    }

}
