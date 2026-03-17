<?php
/*
 * Rose Business Suite - Accounting, CRM and POS Software
 * Copyright (c) UltimateKode.com. All Rights Reserved
 * ***********************************************************************
 *
 *  Email: support@ultimatekode.com
 *  Website: https://www.ultimatekode.com
 *
 *  ************************************************************************
 *  * This software is furnished under a license and may be used and copied
 *  * only  in  accordance  with  the  terms  of such  license and with the
 *  * inclusion of the above copyright notice.
 *  * If you Purchased from Codecanyon, Please read the full License from
 *  * here- http://codecanyon.net/licenses/standard/
 * ***********************************************************************
 */

namespace App\Http\Controllers\API;

use App\Http\Responses\RedirectResponse;
use App\Models\customer\Customer;
use App\Models\transaction\TransactionHistory;
use App\Repositories\Focus\customer\CustomerPassword;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;
use DB;
use Illuminate\Support\Facades\App;
use App\Traits\ApiResponseTrait;


class CategoryController extends Controller
{

    use ApiResponseTrait;
    public function get(Request $request)
    {

        if (!$request->has('paginate')) {

            $paginate = 1000;
        } else {
            $paginate = $request->paginate;
        }
        if (!$request->has('page')) {

            $page = 1;
        } else {
            $page = $request->page;
        }
        $user_id = Auth::id();
        $store_id = $request->store_id ?? 1;
        $categoryIds = \DB::table('category_customer')->where('customer_id', $store_id)->pluck('category_id')->toArray();

        $categories = Category::whereIn('category_id', $categoryIds)
            ->whereNull('parent')
            ->where('active', 1)
            ->with([
                'children' => function ($query) {
                    $query->when(App::getLocale() == 'en', function ($query) {
                        $query->select('*', 'title as name', 'product_categories.category_id as id');
                    });
                    $query->when(App::getLocale() == 'ar', function ($query) {
                        $query->select('*', 'name_ar as name', 'product_categories.category_id as id');
                    });

                    $query->orderBy('order');
                    $query->where('active', 1);
                    $query->with([
                        'children' => function ($q) {
                            $q->join('category_branch', 'category_branch.category_id', '=', 'product_categories.category_id');
                            $q->with([
                                'main_parent' => function ($q) {
                                    $q->with('main_parent');
                                }
                            ]);
                            $q->where('active', 1);
                            $q->when(App::getLocale() == 'ar', function ($query) {
                                $query->select('*', 'name_ar as name', 'product_categories.category_id as id');
                            });
                            $q->when(App::getLocale() == 'en', function ($query) {
                                $query->select('*', 'title as name', 'product_categories.category_id as id');
                            });
                            $q->orderBy('order');
                        }
                    ]);
                }
            ])->when(App::getLocale() == 'en', function ($query) {
                $query->select('*', 'title as name', 'product_categories.category_id as id');
            })->when(App::getLocale() == 'ar', function ($query) {
                $query->select('*', 'name_ar as name', 'image as image', 'product_categories.category_id as id');
            })->orderBy('order')->paginate($paginate);



        $message = App::getLocale() == 'en' ? 'categories returned Successfully' : 'تمت إعادة الفئات بنجاح';
        return $this->successResponse($categories, $message);

    }

    public function search(Request $request)
    {

        if (!$request->has('paginate')) {

            $paginate = 1000;
        } else {
            $paginate = $request->paginate;
        }
        if (!$request->has('page')) {

            $page = 1;
        } else {
            $page = $request->page;
        }
        $user_id = Auth::id();
        $categoryIds = \DB::table('category_customer')->where('customer_id', $user_id)->pluck('category_id')->toArray();
        $categories = Category::where(function ($query) use ($request) {
            $query->where('name_ar', 'LIKE', '%' . $request->search . '%')
                ->orWhere('title', 'LIKE', '%' . $request->search . '%');
        })
            ->whereNull('parent')
            ->where('active', 1)
            ->whereIn('category_id', $categoryIds)
            ->with([
                'children' => function ($query) {
                    $query->when(App::getLocale() == 'en', function ($query) {
                        $query->select('*', 'title as name', 'product_categories.category_id as id');
                    });
                    $query->when(App::getLocale() == 'ar', function ($query) {
                        $query->select('*', 'name_ar as name', 'product_categories.category_id as id');
                    });

                    $query->orderBy('order');
                    $query->where('active', 1);
                    $query->with([
                        'children' => function ($q) {
                            $q->join('category_branch', 'category_branch.category_id', '=', 'product_categories.category_id');
                            $q->with([
                                'main_parent' => function ($q) {
                                    $q->with('main_parent');
                                }
                            ]);
                            $q->where('active', 1);
                            $q->when(App::getLocale() == 'ar', function ($query) {
                                $query->select('*', 'name_ar as name', 'product_categories.category_id as id');
                            });
                            $q->when(App::getLocale() == 'en', function ($query) {
                                $query->select('*', 'title as name', 'product_categories.category_id as id');
                            });
                            $q->orderBy('order');
                        }
                    ]);
                }
            ])->when(App::getLocale() == 'en', function ($query) {
                $query->select('*', 'title as name', 'product_categories.category_id as id');
            })->when(App::getLocale() == 'ar', function ($query) {
                $query->select('*', 'name_ar as name', 'image as image', 'product_categories.category_id as id');
            })->orderBy('order')->paginate($paginate);



        $message = App::getLocale() == 'en' ? 'categories returned Successfully' : 'تمت إعادة الفئات بنجاح';
        return $this->successResponse($categories, $message);

    }
}
