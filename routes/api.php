<?php

use App\Http\Controllers\API\AgentVisitsController;
use App\Http\Controllers\API\AttendanceController;
use App\Http\Controllers\API\PosMaterialController;
use App\Http\Controllers\API\StoreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\API\AuthController;
use \App\Http\Controllers\API\CodeController;
use \App\Http\Controllers\API\CategoryController;
use \App\Http\Controllers\API\InvoiceController;
use App\Http\Controllers\API\NotificationController;
use \App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\TokenController;
use App\Http\Controllers\API\OsaController;
use App\Models\Category;
use App\Models\Customer;
use App\Models\InvocieProduct;
use App\Models\InvocieShelfy;
use App\Models\Product;
use App\Models\ProductOsa;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('delete', function () {
    // InvocieProduct::truncate();
    // InvocieShelfy::truncate();
    //ProductOsa::truncate();
    ProductVariation::truncate();
    Product::truncate();
    // Category::truncate();
    // Customer::where('id', '!=', 1)->delete();

});
Route::post('/removetoken', [AuthController::class, 'removedevicetoken']);
Route::get('status_invoice', function () {
    InvocieShelfy::where('status', 0)->update(['status' => 3]);
});
Route::get('show', function () {
    $product = DB::table('products_osa')->insert([
        'barcode' => json_encode(['11148', '11147', '11150'], JSON_UNESCAPED_SLASHES),
        'segment' => 3,
    ]);
    return response()->json([
        'flag' => 1,
    ], 201);
});
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//==================================== authentication ==================================//
Route::group(['middleware' => ['api'], 'prefix' => 'auth'], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/update', [AuthController::class, 'update']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'get_user_info']);
    Route::post('/update/password', [AuthController::class, 'update_user_password']);
});

//==================================== code ==================================//
Route::post('/send_code', [CodeController::class, 'send_code']);
Route::post('/validate_code', [CodeController::class, 'validate_code']);


Route::prefix('webhook')->group(function () {
    Route::post('/expire/token', [AuthController::class, 'expireCustomer']);
    Route::post('/unexpire/token', [AuthController::class, 'unExpireCustomer']);
});
//==================================== auth:api ==================================//
// Route::group(['middleware' => 'auth:api'], function () {
Route::middleware(['auth:api', 'customer.expired'])->group(function () {
    //==================================== category ==================================//
    Route::get('/categories', [CategoryController::class, 'get']);
    Route::get('/categories/search', [CategoryController::class, 'search']);
    Route::post('/token', [TokenController::class, 'store_token']);

    //==================================== Invoic ==================================//
    Route::post('/add_invoice', [InvoiceController::class, 'add_invoice']);
    Route::post('/invoices/{invoice_id}/images', [InvoiceController::class, 'storeImage']);
    Route::delete('/delete_invoice', [InvoiceController::class, 'delete_invoice']);
    Route::post('/add_item', [InvoiceController::class, 'add_item']);
    Route::delete('/delete_item', [InvoiceController::class, 'delete_item']);
    Route::get('/invoices', [InvoiceController::class, 'get_invoices']);
    Route::get('/invoice_notification', [InvoiceController::class, 'get_invoice_notification']);
    Route::get('/invoices/{id}', [InvoiceController::class, 'get_invoice_by_id']);
    Route::get('/finish_invoice/{id}', [InvoiceController::class, 'finish_invoice']);

    //==================================== product ==================================//
    Route::get('/get_product_barcode', [ProductController::class, 'get_product_barcode']);
    //==================================== osa ==================================//
    Route::post('/osa', [OsaController::class, 'add_osa']);
    Route::get('/osa/{id}', [OsaController::class, 'get_osa']);
    Route::get('v2/osa/{id}', [OsaController::class, 'getOsaV2']);


    //==================================== Stores ==================================//
    //
    Route::get('/stores', [StoreController::class, 'index']);


    //==================================== notification ==================================//
    Route::prefix('notification')->group(function () {

        Route::get('/', [NotificationController::class, 'index']);
        Route::post('/read', [NotificationController::class, 'readNotification']);
        Route::post('/read/all', [NotificationController::class, 'readAllNotifications']);
    });


    Route::prefix('visits')->group(function () {
        Route::get('/', [AgentVisitsController::class, 'index']);
        Route::get('/data', [AgentVisitsController::class, 'getVisitData']);
        Route::post('/start', [AgentVisitsController::class, 'startVisit']);
        Route::post('/end', [AgentVisitsController::class, 'endVisit']);
        Route::post('/cancel', [AgentVisitsController::class, 'cycleCancelation']);
        Route::post('/returns', [AgentVisitsController::class, 'visitReturnes']);
        Route::delete('remove/returns', [AgentVisitsController::class, 'removeReturn']);
        Route::post('/add/osa', [AgentVisitsController::class, 'addVisitOsa']);
        Route::post('/add/item', [AgentVisitsController::class, 'addItem']);
        Route::delete('/remove/item', [AgentVisitsController::class, 'removeItem']);
        Route::post('/attendance', [AttendanceController::class, 'addAttendance']);
        Route::post('/pos/material', [PosMaterialController::class, 'addMaterial']);
        Route::delete('/pos/remove/image', [PosMaterialController::class, 'removePosImage']);
    });
});
