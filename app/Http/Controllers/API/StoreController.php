<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerStore;
use App\Models\Store;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;


class StoreController extends Controller
{
    use ApiResponseTrait;

    //
    // public function __construct()
    // {
    //     $this->middleware('auth:api');
    // }
    public function index(Request $request)
    {
        $user = auth()->user();
        // dd($user);
        $search = $request->search;

        $ids_stores = CustomerStore::where('customer_id', $user->id)->pluck('store_id');
        // dd($ids_stores);
        $search = str_replace(['ى'], 'ي', $search);
        $search = str_replace(['أ', 'إ'], 'ا', $search);
        $search = str_replace(['ة'], 'ه', $search);
        $stores = Store::whereIn('id', $ids_stores)->
            when($search, function ($query) use ($search) {
                $query->whereRaw("
            REPLACE(REPLACE(REPLACE(REPLACE(name, 'أ', 'ا'), 'إ', 'ا'), 'ى', 'ي'), 'ة', 'ه') LIKE ?
        ", ["%{$search}%"]);
            })
            ->paginate($request->per_page ?? 100);

        // return response()->json($stores, 200);
        $message = App::getLocale() == 'en' ? 'Stores returned Successfully' : 'تمت إعادة المتاجر بنجاح';
        return $this->successResponse($stores, $message);

    }

}
