<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\InvoiceNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\App;
use App\Traits\ApiResponseTrait;

use App\Models\InvocieShelfy;
use App\Models\InvoiceImage;
use App\Models\InvocieProduct;
use App\Models\InvoiceCategory;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManager;

class InvoiceController extends Controller
{
    use ApiResponseTrait;

    public function storeImage(Request $request, $invoice_id)
    {
        $rules = [
            'images' => 'required',
            'images.*' => 'required|file|mimes:jpeg,png,jpg,gif,svg',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->errorResponse('validation Error', 422, $validator->errors());
        }
        $images = $request->file('images');
        $imagePaths = [];
        foreach ($images as $image) {
            // $sizeInKB = round($image->getSize() / 1024, 2);

            $path = $this->saveImage($image, 'invoices');
            $imagePaths[] = $path;

            // image dimention and size
            $fullImagePath = public_path('invoices/' . basename($path));
            // [$width, $height] = getimagesize($fullImagePath);
            $this->reduceImageSizeNative($fullImagePath, 40);

            InvoiceImage::create([
                'invoice_id' => $invoice_id,
                'image' => $path,
            ]);
        }
        return $imagePaths;

    }

    // ======================================== add new invoice ===========================================================
    public function add_invoice(Request $request)
    {

        $rules = [
            'invoice_id' => 'required',
            'category_id' => 'required',
            'images' => 'required',
            'images.*' => 'required|file|mimes:jpeg,png,jpg,gif,svg',
            'amount' => 'required|numeric',
        ];
        $validator = Validator::make($request->all(), $rules);
        // dd($request->all());

        if ($validator->fails()) {
            return $this->errorResponse('validation Error', 422, $validator->errors());
        }

        $user_id = Auth::id();
        $invoice = InvocieShelfy::create([
            'customer_id' => $user_id,
            'invoice_id' => $request->invoice_id,
            'status' => 3,
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            // 'agentid' => auth('api')->user()->id,
        ]);
        $this->deleteImage($invoice->image);

        InvoiceImage::where('invoice_id', $invoice->id)->delete();

        $imagePaths = $this->storeImage($request, $invoice->id);
        // $images = $request->file('images');
        // $imagePaths = [];
        // foreach ($images as $image) {
        //     // $sizeInKB = round($image->getSize() / 1024, 2);

        //     $path = $this->saveImage($image, 'invoices');
        //     $imagePaths[] = $path;

        //     // image dimention and size
        //     $fullImagePath = public_path('invoices/' . basename($path));
        //     // [$width, $height] = getimagesize($fullImagePath);
        //     $this->reduceImageSizeNative($fullImagePath, 40);

        //     InvoiceImage::create([
        //         'invoice_id' => $invoice->id,
        //         'image' => $path,
        //     ]);

        //     // $imageInfo[] = [
        //     //     'path' => $path,
        //     //     'width' => $width,
        //     //     'height' => $height,
        //     //     'size_kb' => $sizeInKB,
        //     // ];

        //     // Log::info($imageInfo);
        // }


        $message = App::getLocale() == 'en' ? 'invoice stored successfully' : 'تم حفظ الفاتورة بنجاح';
        $data = [
            'invoice' => $invoice,
            'images' => $imagePaths,

        ];
        return $this->successResponse($data, $message, 200);
    }



    public function delete_invoice(Request $request)
    {
        // Validate that an invoice_id is provided
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:invocies_shelfy,id',
        ], [
            'id.required' => 'هذاالحقل مطلوب',  // Custom message for this specific rule
            'id.exists' => 'الرقم المحدد غير صحيح.',  // Custom message for this specific rule
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation Error', 422, $validator->errors());
        }

        // Find the invoice
        $invoice = InvocieShelfy::find($request->id);

        // Check if the invoice exists
        if (!$invoice) {
            return $this->errorResponse('الفاتورة غير موجودة', 404);
        }

        // Delete associated images
        $images = $invoice->images; // Assuming the relation 'images' is set up in the InvoiceShelfy model

        foreach ($images as $image) {
            $imagePath = public_path('invoices/' . basename($image->image));
            if (file_exists($imagePath)) {
                unlink($imagePath);  // Delete the image file
            }
            $image->delete();  // Delete image record from the database
        }

        // Delete the invoice record
        $invoice->delete();

        // Return a success response
        $message = App::getLocale() == 'en' ? 'Invoice deleted successfully' : 'تم حذف الفاتورة بنجاح';
        return $this->successResponse([], $message, 200);
    }


    public function reduceImageSizeNative($filePath, $quality = 50)
    {
        if (!file_exists($filePath))
            return false;

        $mime = mime_content_type($filePath);
        $image = null;

        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                $image = imagecreatefromjpeg($filePath);
                imagejpeg($image, $filePath, $quality); // 0–100
                break;

            case 'image/png':
                $image = imagecreatefrompng($filePath);
                $compression = 9 - round($quality / 10); // convert to 0–9
                imagepng($image, $filePath, $compression); // 0–9
                break;

            case 'image/webp':
                $image = imagecreatefromwebp($filePath);
                imagewebp($image, $filePath, $quality); // 0–100
                break;

            case 'image/gif':
                $image = imagecreatefromgif($filePath);
                imagegif($image, $filePath); // no quality param, just re-save
                break;

            case 'image/bmp':
                $image = imagecreatefrombmp($filePath);
                imagebmp($image, $filePath); // no quality param, just re-save
                break;

            default:
                return false; // unsupported format
        }

        if ($image) {
            imagedestroy($image);
            return true;
        }

        return false;
    }


    // ======================================== add product invoice ===========================================================
    // public function add_item(Request $request)
    // {
    //     $user_id = Auth::id();
    //     $rules = [
    //         'id' => 'required',
    //         'invoice_id' => 'required',
    //     ];
    //     $validator = Validator::make($request->all(), $rules);
    //     if ($validator->fails()) {
    //         return $this->errorResponse('validation Error', 422, $validator->errors());
    //     }
    //     $path = '';
    //     if (isset($request->image)) {
    //         $path = $this->saveImage($request->file('image'), 'invoices');
    //     }
    //     $product = product::where('id', $request->id)->first();
    //     if (!is_null($product)) {
    //         $invoice_product = InvocieProduct::updateOrCreate([
    //             'id_erp' => $product->id_erp,
    //             'invoice_id' => $request->invoice_id,
    //         ], [
    //             'id_erp' => $product->id_erp,
    //             'invoice_id' => $request->invoice_id,
    //             'image' => $path,
    //         ]);

    //         $invoice = InvocieShelfy::with([
    //             'products' => function ($query) {
    //                 $query->with([
    //                     'product' => function ($query) {
    //                         $query->with([
    //                             'segements' => function ($q) {
    //                                 $q->where('segment_id', Auth::user()->segment);
    //                                 // $q->with('segment_name');
    //                             },
    //                             'standard' => function ($q) {
    //                                 $q->select('product_id', 'image', 'barcode');
    //                                 // $q->groupBy('product_variations.barcode')->distinct();
    //                             },
    //                         ])
    //                             ->when(App::getLocale() == 'en', function ($query) {
    //                                 $query->select(['id_erp', 'products.id as id', 'products.id as id', 'products.name as name', 'product_des as description']);
    //                             })
    //                             ->when(App::getLocale() == 'ar', function ($query) {
    //                                 $query->select(['id_erp', 'products.id as id', 'products.name_ar as name', 'description_ar as description']);
    //                             });
    //                     }
    //                 ]);
    //             },
    //             'images'
    //         ])->where('customer_id', $user_id)->where('invoice_id', $request->invoice_id)->get();


    //         $message = App::getLocale() == 'en' ? 'InvocieProduct stored successfully' : 'تم حفظ المنتج بنجاح';
    //         return $this->successResponse($invoice, $message, 200);
    //     } else {
    //         $message = App::getLocale() == 'en' ? 'Product Not Found' : 'المنتج غير موجود';
    //         return $this->errorResponse($message, 401, []);
    //     }
    // }

    public function add_item(Request $request)
    {
        $user_id = Auth::id();
        $productIds = explode(',', $request->input('id'));
        $rules = [
            'id' => 'required|string',
            'invoice_id' => 'required|exists:invocies_shelfy,id',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category_id' => 'required|exists:product_categories,category_id',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->errorResponse('validation Error', 422, $validator->errors());
        }
        $paths = [];
        if (isset($request->images)) {
            foreach ($request->images as $image) {
                $path = $this->saveImage($image, 'invoices');
                $fullImagePath = public_path('invoices/' . basename($path));
                $this->reduceImageSizeNative($fullImagePath, 40);
                $paths[] = $path;
            }
        }
        $products = product::whereIn('id', $productIds)->get()->pluck('id_erp')->toArray();

        if (!is_null($products)) {
            foreach ($products as $product) {
                $invoice_product = InvocieProduct::updateOrCreate([
                    'id_erp' => $product,
                    'invoice_id' => $request->invoice_id,
                ], [
                    'id_erp' => $product,
                    'invoice_id' => $request->invoice_id,
                    'image' => null,
                ]);
            }


            $invoice = InvocieShelfy::with([
                'products' => function ($query) {
                    $query->with([
                        'product' => function ($query) {
                            $query->with([
                                'segements' => function ($q) {
                                    $q->where('segment_id', Auth::user()->segment);
                                    // $q->with('segment_name');
                                },
                                'standard' => function ($q) {
                                    $q->select('product_id', 'image', 'barcode');
                                    // $q->groupBy('product_variations.barcode')->distinct();
                                },
                            ])
                                ->when(App::getLocale() == 'en', function ($query) {
                                    $query->select(['id_erp', 'products.id as id', 'products.id as id', 'products.name as name', 'product_des as description']);
                                })
                                ->when(App::getLocale() == 'ar', function ($query) {
                                    $query->select(['id_erp', 'products.id as id', 'products.name_ar as name', 'description_ar as description']);
                                });
                        }
                    ]);
                },
                'images'
            ])->where('customer_id', $user_id)->where('id', $request->invoice_id)->first();
            // dd($invoice);


            // $invoiceCategory = $invoice->invoiceCategory()->updateOrCreate(
            //     [
            //         'category_id' => $request->category_id
            //     ],
            //     [
            //         'category_id' => $request->category_id
            //     ]
            // );
            $invoiceCategory = InvoiceCategory::updateOrCreate(
                ['invoice_id' => $invoice->id, 'category_id' => $request->category_id],
                ['invoice_id' => $invoice->id, 'category_id' => $request->category_id]
            );

            if (isset($invoiceCategory)) {
                foreach ($paths as $path) {
                    $invoiceCategory->invoiceCategoryImages()->create([
                        'image' => $path
                    ]);
                }
                // $invoiceCategory->invoiceCategoryImages()->sync($paths);
            }

            $categoryId = $request->category_id;

            $invoice->load([
                'invoiceCategory' => function ($q) use ($categoryId) {
                    $q->select('id', 'invoice_id', 'category_id')
                        ->where('category_id', $categoryId);
                },
                'invoiceCategory.invoiceCategoryImages' => function ($q) {
                    $q->select('id', 'invoice_category_id', 'image');
                }
            ]);
            $message = App::getLocale() == 'en' ? 'InvocieProduct stored successfully' : 'تم حفظ المنتج بنجاح';
            return $this->successResponse($invoice, $message, 200);
        } else {
            $message = App::getLocale() == 'en' ? 'Product Not Found' : 'المنتج غير موجود';
            return $this->errorResponse($message, 401, []);
        }
    }
    // ======================================== delete product invoice ===========================================================
    public function delete_item(Request $request)
    {
        $rules = [
            'id' => 'required',
            'invoice_id' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->errorResponse('validation Error', 422, $validator->errors());
        }

        $product = Product::where('id', $request->id)->first();

        InvocieProduct::where('invoice_id', $request->invoice_id)->where('id_erp', $product->id_erp)->delete();

        $message = App::getLocale() == 'en' ? 'InvocieProduct deleted successfully' : 'تم حذف المنتج بنجاح';
        return $this->successResponse([], $message, 200);
    }
    // ======================================== get all invoice ===========================================================
    public function get_invoices(Request $request)
    {
        $user_id = Auth::id();
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
        $invoices = InvocieShelfy::with([
            'products' => function ($query) {
                $query->with([
                    'product' => function ($query) {
                        $query->with([
                            'segements' => function ($q) {
                                $q->where('segment_id', Auth::user()->segment);

                                // $q->with('segment_name');
                            },
                            'standard' => function ($q) {
                                $q->select('product_id', 'image', 'barcode');
                                // $q->groupBy('product_variations.barcode')->distinct();
                            },
                        ])
                            ->when(App::getLocale() == 'en', function ($query) {
                                $query->select(['id_erp', 'products.id as id', 'products.id as id', 'products.name as name', 'product_des as description']);
                            })
                            ->when(App::getLocale() == 'ar', function ($query) {
                                $query->select(['id_erp', 'products.id as id', 'products.name_ar as name', 'description_ar as description']);
                            });
                    }
                ]);
            },
            'images'
        ])->where('customer_id', $user_id)->orderBy('id', 'DESC')->paginate($paginate);

        $message = App::getLocale() == 'en' ? 'Invocies returned successfully' : 'تم استرجاع الفواتير بنجاح';
        $data = [
            'invoices' => $invoices,
            'points' => Auth::user()->points
        ];
        return $this->successResponse($data, $message, 200);
    }
    // ======================================== get all invoice ===========================================================
    public function get_invoice_by_id($id)
    {
        $user_id = Auth::id();

        $invoice = InvocieShelfy::with([
            'products' => function ($query) {
                $query->with([
                    'product' => function ($query) {
                        $query->with([
                            'segements' => function ($q) {
                                $q->where('segment_id', Auth::user()->segment);
                                // $q->with('segment_name');
                            },
                            'standard' => function ($q) {
                                $q->select('product_id', 'image', 'barcode');
                                // $q->groupBy('product_variations.barcode')->distinct();
                            },
                        ])
                            ->when(App::getLocale() == 'en', function ($query) {
                                $query->select(['id_erp', 'products.id as id', 'products.id as id', 'products.productcategory_id', 'products.name as name', 'product_des as description']);
                            })
                            ->when(App::getLocale() == 'ar', function ($query) {
                                $query->select(['id_erp', 'products.id as id', 'products.name_ar as name', 'products.productcategory_id', 'description_ar as description']);
                            });
                    }
                ]);
            },
            'images'
        ])->where('id', $id)->get();

        $message = App::getLocale() == 'en' ? 'Invocie returned successfully' : 'تم استرجاع الفاتورة بنجاح';
        return $this->successResponse($invoice, $message, 200);
    }


    public function get_invoice_notification()
    {
        $user_id = Auth::id();
        //         $invoices = InvoiceNotification::with(['invoices_point:id,points'])->get();
        // dd($notification->customer);
        $invoices = InvoiceNotification::with('customer:points')->where('user_id', $user_id)
            ->orderBy('id', 'DESC')
            ->get();
        $message = App::getLocale() == 'en' ? 'Notification returned successfully' : 'تم استرجاع الاشعارات  بنجاح';

        return $this->successResponse($invoices, $message, 200);
    }



    public function finish_invoice($id)
    {
        $user_id = Auth::id();

        $invoice = InvocieShelfy::where('id', $id)->update(['status' => 4]);


        $message = App::getLocale() == 'en' ? 'Invocie returned successfully' : 'تم استرجاع الفاتورة بنجاح';
        return $this->successResponse($invoice, $message, 200);
    }
}
