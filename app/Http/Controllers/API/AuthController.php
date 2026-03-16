<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Hash;
use App\Models\Customer;
use App\Models\Token;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;




use Validator;

class AuthController extends Controller
{
    use ApiResponseTrait;

    // --------------------------------------------- login -----------------------------------------------------------------
    public function login(Request $request)
    {
        $rules = [
            'phone' => 'required',
            'password' => 'required|min:6',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->errorResponse('validation Error', 422, $validator->errors());
        }

        $email = Customer::where('phone', $request->phone)->pluck('email')->first();

        $customer = Customer::where('phone', $request->phone)->first();
        if (!$customer) {
            $message = App::getLocale() == 'en' ? 'Customer not found' : 'العميل غير موجود';
            return $this->errorResponse($message, 404);
        } else {

            if ($customer->store_status == 0) {
                $message = App::getLocale() == 'en' ? 'Customer not active' : 'العميل غير نشط';
                return $this->errorResponse($message, 404);
            }
        }
        $credentials = [
            'email' => $email,
            'password' => $request->password,
        ];
        if (!$token = Auth::guard('api')->attempt($credentials)) {
            $message = App::getLocale() == 'en' ? 'There is an error with the phone number or password' : 'هناك خطأ ما في الهاتف أو كلمة المرور';
            return $this->errorResponse($message, 401);
        }
        $message = App::getLocale() == 'en' ? 'successfully signed in' : 'تم تسجيل الدخول بنجاح';
        $data = [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60000,
            'user' => auth('api')->user(),
        ];
        return $this->successResponse($data, $message);
    }


    public function expireCustomer(Request $request)
    {
        Log::info("expireCustomer called", ['request_data' => $request->all()]);

        $customer = Customer::find($request->customerId);

        if (!$customer) {
            $message = App::getLocale() == 'en' ? 'Customer not found' : 'العميل غير موجود';
            Log::warning("expireCustomer: Customer not found", ['customerId' => $request->customerId]);
            return $this->errorResponse($message, 404);
        }

        // Blacklist this customer
        // Cache::put("expired_customer_{$customer->id}", true, now()->addDay());
        Cache::forever("expired_customer_{$customer->id}", true);
        Log::info("expireCustomer: Customer forcibly expired", [
            'customerId' => $customer->id,
            'cache_key' => "expired_customer_{$customer->id}"
        ]);

        $message = App::getLocale() == 'en'
            ? 'Customer sessions expired successfully'
            : 'تم إنهاء جميع جلسات العميل بنجاح';

        return $this->successResponse([], $message);
    }

    public function unExpireCustomer(Request $request)
    {
        $agent = Customer::find($request->customerId);
        Cache::forget("expired_customer_{$agent->id}");
        Log::info("Customer {$agent->id} re-activated and removed from blacklist");
    }


    // --------------------------------------------- update password-----------------------------------------------------------------
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('validation Error', 422, $validator->errors());
        }

        $customer = Customer::where('phone', $request->phone)->first();
        if (!$customer) {
            $message = App::getLocale() == 'en' ? 'Customer not found' : 'العميل غير موجود';
            return $this->errorResponse($message, 404);
        } else {
            if ($customer->store_status == 0) {
                $message = App::getLocale() == 'en' ? 'Customer not active' : 'العميل غير نشط';
                return $this->errorResponse($message, 404);
            }
        }
        $customer->password = Hash::make($request->password);
        $customer->save();

        $credentials = [
            'email' => $customer->email,
            'password' => $request->password,
        ];
        if (!$token = Auth::guard('api')->attempt($credentials)) {
            $locale = App::getLocale();
            $message = $locale == 'en' ? 'There is an error with the phone number or password' : 'هناك خطأ ما في الهاتف أو كلمة المرور';
            return $this->errorResponse($message, 401);
        }
        //  dd($credentials);
        $data = [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60000,
            'user' => auth('api')->user(),
        ];
        $message = App::getLocale() == 'en' ? 'Password updated successfully' : 'تم تحديث كلمة المرور بنجاح';
        return $this->successResponse($data, $message, 200);
    }
    // ---------------------------------------------update password  user-----------------------------------------------------------------
    public function update_user_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string|min:8',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
        ]);
        if ($validator->fails()) {
            return $this->errorResponse('validation Error', 422, $validator->errors());
        }
        if (!Hash::check($request->old_password, auth('api')->user()->password)) {
            $message = App::getLocale() == 'en' ? 'Old password not match' : 'كلمة المرور القديمة غير متطابقة';
            return $this->errorResponse($message, 422);
        }
        if ($request->password != $request->password_confirmation) {
            $message = App::getLocale() == 'en' ? 'Password not match' : 'كلمة المرور غير متطابقة';
            return $this->errorResponse($message, 422);
        }
        $customer = Auth::guard('api')->user();
        $customer->password = Hash::make($request->password);
        $customer->save();
        $data = [
            'user' => auth('api')->user(),
        ];
        $message = App::getLocale() == 'en' ? 'Password updated successfully' : 'تم تحديث كلمة المرور بنجاح';
        return $this->successResponse($data, $message, 200);
    }

    // --------------------------------------------- log out-----------------------------------------------------------------
    public function logout(Request $request)
    {
        $user = auth('api')->user();

        $token_check = Token::where('user_id', $user->id)->where('token', $request->token)->first();
        if ($token_check) {
            Token::where('user_id', $user->id)->delete();
        }
        auth('api')->logout();
        $message = App::getLocale() == 'en' ? 'successfully signed out' : 'تم تسجيل الخروج بنجاح';
        return $this->successResponse([], $message);
    }
    // --------------------------------------------- user info-----------------------------------------------------------------
    public function get_user_info()
    {
        $user = Auth::guard('api')->user();
        if (!$user) {
            $message = App::getLocale() == 'en' ? 'User not authenticated' : 'لم تتم مصادقة المستخدم';
            return $this->errorResponse('User not authenticated', 401);
        }
        $user = $user->only(['id', 'name', 'email', 'phone', 'store_name']);
        $message = App::getLocale() == 'en' ? 'user info returned successfully' : 'تم استرجاع بيانات المستخدم بنجاح';
        return $this->successResponse($user, $message);
    }

    // removedevicetoken
    public function removedevicetoken(Request $request)
    {
        $request->validate([
            'token' => 'required',
        ]);
        $deletedCount = Token::where('token', $request->token)->delete();

        if ($deletedCount > 0) {
            $message = App::getLocale() == 'en' ? 'Delete token successfully' : 'تم حذف التوكن بنجاح';
            return $this->successResponse([], $message);
        } else {
            $message = App::getLocale() == 'en' ? 'Token not found' : 'التوكن غير موجود';
            return $this->successResponse([], $message);
        }
    }
}
