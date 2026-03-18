<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AgentVisits\AddVisitItemRequest;
use App\Http\Requests\AgentVisits\CancelVisitCycleRequest;
use App\Http\Requests\AgentVisits\GetVisitDataRequest;
use App\Http\Requests\AgentVisits\RemoveReturnRequest;
use App\Http\Requests\AgentVisits\ScanPackRequest;
use App\Http\Requests\AgentVisits\ShelfPercentageRequest;
use App\Http\Requests\AgentVisits\StartVisitRequest;
use App\Http\Requests\AgentVisits\StoreVisitProductPriceRequest;
use App\Http\Requests\AgentVisits\VisitOsaRequest;
use App\Http\Requests\AgentVisits\VisitReturnsRequest;
use App\Http\Resources\AgentVisits\VisitOsaResource;
use App\Http\Resources\AgentVisits\VisitReturnsResource;
use App\Http\Resources\AgentVisits\VisitsResource;
use App\Http\Resources\Products\ProductResource;
use App\Models\Category;
use App\Models\PackProduct;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\ScanPackProduct;
use App\Models\ScanPromotionProduct;
use App\Models\Visit;
use App\Models\VisitProductPrice;
use App\Models\CategoryShelfPercentage;
use App\Traits\ApiResponseTrait;
use App\Traits\ImageHandlingTrait;
use Illuminate\Http\Request;

class AgentVisitsController extends Controller
{
    use ApiResponseTrait, ImageHandlingTrait;

    // test
    public function index()
    {
        $authUser = auth()->user()->id;
        $visitsForToday = Visit::where('agent_id', $authUser)->
            with('store')->
            whereDate('created_at', now()->toDateString())
            ->orderBy('order', 'asc')
            ->get();

        $resource = VisitsResource::collection($visitsForToday);
        return $this->successResponse($resource);
    }

    public function startVisit(StartVisitRequest $request)
    {
        $visit = Visit::find($request->visit_id);
        if ($visit->start_time == null) {
            $visit->start_time = now();
            $visit->save();
            return $this->successResponse([], 'Visit started successfully');
        }
        return $this->successResponse([], 'Visit already started');
    }

    public function endVisit(StartVisitRequest $request)
    {
        $visit = Visit::find($request->visit_id);
        if ($visit->end_time == null) {
            $visit->end_time = now();
            $visit->save();
            return $this->successResponse([], 'Visit ended successfully');
        }
        $visit->load('product');
        return $this->successResponse([], 'Visit already ended');
    }

    public function visitReturnes(VisitReturnsRequest $request)
    {
        $visit = Visit::find($request->visit_id);
        $product = Product::find($request->product_id);
        $returnItem = $visit->returnItems()->create([
            'product_id' => $product->id_erp
        ]);

        foreach ($request->expirations as $expiration) {
            $returnItem->expirationDates()->create([
                'expiration_date' => $expiration['expire_date'],
                'quantity' => $expiration['quantity']
            ]);
        }
        $resource = new VisitReturnsResource($returnItem->load('product', 'expirationDates'));
        return $this->successResponse($resource, 'Returned items added successfully');
    }

    public function removeReturn(RemoveReturnRequest $request)
    {
        $visit = Visit::find($request->visit_id);
        $product = Product::find($request->product_id);
        $visit->returnItems()->where('product_id', $product->id_erp)->delete();
        return $this->successResponse([], 'Return item removed successfully');
    }

    public function addVisitOsa(VisitOsaRequest $request)
    {
        $visit = Visit::find($request->visit_id);
        $product = Product::find($request->product_id);
        $osaVisit = $visit->osaVisits()->updateOrCreate(
            [
                'product_id' => $product->id_erp, // match condition
            ],
            [
                'status' => $request->status,
                'note' => $request->note
            ]
        );
        $resource = new VisitOsaResource($osaVisit->load('product'));
        return $this->successResponse($resource, 'Osa visit added successfully');
    }

    // public function addItem(AddVisitItemRequest $request)
    // {
    //     $visit = Visit::find($request->visit_id);
    //     $visitItem = $visit->visitItems()->create([
    //         'product_ids' => $request->product_ids
    //     ]);
    //     $productIds = explode(',', $request->product_ids);
    //     foreach ($productIds as $productId) {
    //         $visitItem->VisitItemProducts()->create([
    //             'product_id' => $productId
    //         ]);
    //     }
    //     foreach ($request->images_before as $key => $imageBefore) {
    //         $imageBefore = $this->saveImage($imageBefore, 'visit_items/before');
    //         $imageAfter = $this->saveImage($request->images_after[$key], 'visit_items/after');
    //         $visitItem->VisitItemPlanograms()->create([
    //             'before_image' => $imageBefore,
    //             'after_image' => $imageAfter
    //         ]);
    //     }
    //     return $this->successResponse([], 'Visit items added successfully');
    // }

    public function addItem(AddVisitItemRequest $request)
    {
        $visit = Visit::findOrFail($request->visit_id);

        // 1️⃣ Remove existing items and planograms for this visit
        foreach ($visit->visitItems as $existingItem) {

            if ($existingItem->category_id == $request->category_id) {
                foreach ($existingItem->VisitItemPlanograms as $planogram) {
                    $imageBefore = $this->deleteImage($planogram->before_image, 'visit_items/before');
                    $imageAfter = $this->deleteImage($planogram->after_image, 'visit_items/after');
                }
                // Delete planograms
                $existingItem->VisitItemPlanograms()->delete();

                // Delete products
                $existingItem->VisitItemProducts()->delete();

                // Delete the visit item itself
                $existingItem->delete();
            }
        }

        // 2️⃣ Create new visit item
        $visitItem = $visit->visitItems()->create([
            'product_ids' => $request->product_ids,
            'category_id' => $request->category_id
        ]);

        // 3️⃣ Insert products
        $productIds = explode(',', $request->product_ids);
        foreach ($productIds as $productId) {
            $visitItem->VisitItemProducts()->create([
                'product_id' => $productId,
                'category_id' => $request->category_id

            ]);
        }

        // 4️⃣ Insert planograms
        // foreach ($request->images_before as $key => $imageBefore) {
        //     $imageBefore = $this->saveImage($imageBefore, 'visit_items/before');
        //     $imageAfter  = $this->saveImage($request->images_after[$key], 'visit_items/after');

        //     $visitItem->VisitItemPlanograms()->create([
        //         'before_image' => $imageBefore,
        //         'after_image'  => $imageAfter
        //     ]);
        // }

        foreach ($request->images_after as $key => $imageAfter) {
            $imageAfter = $this->saveImage($imageAfter, 'visit_items/after');
            $visitItem->VisitItemPlanograms()->create([
                'after_image' => $imageAfter
            ]);
        }
        foreach ($request->images_before as $key => $imageBefore) {
            $imageBefore = $this->saveImage($imageBefore, 'visit_items/before');
            $visitItem->VisitItemPlanograms()->create([
                'before_image' => $imageBefore,
            ]);
        }


        return $this->successResponse([], 'Visit items added successfully');
    }


    // public function removeItem(Request $request)
    // {
    //     $visit = Visit::with('visitItems.VisitItemProducts')->where('id', $request->visit_id)->first();
    //     $visitItem = $visit->visitItems()->where('category_id', $request->category_id)->first();
    //     $productIds = explode(',', $visitItem->product_ids);
    //     $productIds = array_filter($productIds, function ($id) use ($request) {
    //         return $id != $request->product_id;
    //     });

    //     // Save updated string
    //     $visitItem->product_ids = implode(',', $productIds);
    //     $visitItem->save();
    //     $visitItemProduct = $visitItem->VisitItemProducts()->where('product_id', $request->product_id)->delete();
    //     return $this->successResponse([], 'Visit items removed successfully');
    // }

    public function removeItem(Request $request)
    {
        $visit = Visit::with('visitItems.VisitItemProducts')
            ->where('id', $request->visit_id)
            ->first();

        $visitItem = $visit->visitItems()
            ->where('category_id', $request->category_id)
            ->first();

        if (!$visitItem) {
            return $this->errorResponse('Visit item not found', 404);
        }

        $productIds = explode(',', $visitItem->product_ids);
        $productIds = array_filter($productIds, function ($id) use ($request) {
            return $id != $request->product_id;
        });

        if (empty($productIds)) {
            // Delete visit item completely if no products left
            $visitItem->VisitItemProducts()->delete(); // delete related products
            $visitItem->delete();
        } else {
            // Update remaining products
            $visitItem->product_ids = implode(',', $productIds);
            $visitItem->save();
            $visitItem->VisitItemProducts()->where('product_id', $request->product_id)->delete();
        }

        return $this->successResponse([], 'Visit item removed successfully');
    }

    public function getVisitData(GetVisitDataRequest $request)
    {
        $visit = Visit::with([
            'agentAttendances',
            'returnItems',
            'store',
            'posMaterials.images',
            'visitItems.VisitItemProducts.product',
            'scanPackProducts.product',
            'scanPackProducts.variation',
            'posMs.images',
            'scanPromotionProducts.product',
            'scanPromotionProducts.variation',
            'osaVisits',
            'productPrices.product',
            'shelfPercentage',
        ])->where('id', $request->visit_id)->first();
        $resource = new VisitsResource($visit);
        return $this->successResponse($resource, 'Visit data returned successfully');
    }

    public function cycleCancelation(CancelVisitCycleRequest $request)
    {
        $visit = Visit::with('agentAttendances', 'returnItems', 'visitItems.VisitItemPlanograms', 'osaVisits', 'posMaterials')->where('id', $request->visit_id)->first();
        foreach ($visit->visitItems as $item) {
            foreach ($item->VisitItemPlanograms as $planogram) {
                $imageBefore = $this->deleteImage($planogram->before_image, 'visit_items/before');
                $imageAfter = $this->deleteImage($planogram->after_image, 'visit_items/after');
            }
            $item->VisitItemPlanograms()->delete();
        }
        $visit->agentAttendances()->delete();
        $visit->returnItems()->delete();
        $visit->visitItems()->delete();
        $visit->osaVisits()->delete();
        $visit->posMaterials()->delete();
        $visit->start_time = null;
        $visit->save();
        return $this->successResponse([], 'Visit cancelled successfully');
    }
    public function storeVisitProductPrice(StoreVisitProductPriceRequest $request)
    {
        $visit = Visit::where('id', $request->visit_id)->first();
        if (!$visit) {
            return $this->errorResponse('Visit not found', 404);
        }

        if ((int) $visit->store_id !== (int) $request->store_id) {
            return $this->errorResponse('store_id does not match this visit', 422);
        }
        // barcode
        $barcode = $request->barcode;
        $variation = ProductVariation::where('barcode', $barcode)->first();
        if (!$variation) {
            return $this->errorResponse('Barcode not found', 404);
        }

        $isPack = PackProduct::where('product_id', $variation->product_id)
            ->where('is_pack', 1)
            ->exists();

        if (!$isPack) {
            return $this->errorResponse('This product is not a pack', 404);
        }


        $price = VisitProductPrice::updateOrCreate(
            [
                'visit_id' => (int) $request->visit_id,
                'store_id' => (int) $request->store_id,
                'product_id' => (int) $variation->product_id,
            ],
            [
                'price' => (float) $request->price,
            ]
        );

        return $this->successResponse($price, 'Visit product price saved successfully', 200);
    }

    public function scan(Request $request)
    {
        $barcode = trim((string) $request->barcode);

        $variation = ProductVariation::where('barcode', $barcode)->first();
        if (!$variation) {
            return $this->errorResponse('Barcode not found', 404);
        }

        $product = Product::with(['standard', 'category'])->find($variation->product_id);
        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }
        return $this->successResponse($product, 'Product returned successfully', 200);
    }


    public function scanPack(ScanPackRequest $request)
    {
        $barcode = trim((string) $request->barcode);

        $variation = ProductVariation::where('barcode', $barcode)->first();
        if (!$variation) {
            return $this->errorResponse('Barcode not found', 404);
        }

        $visit = Visit::find($request->visit_id);
        if (!$visit) {
            return $this->errorResponse('Visit not found', 404);
        }

        if ((int) $visit->store_id !== (int) $request->store_id) {
            return $this->errorResponse('Store does not match visit', 422);
        }

        $isPack = PackProduct::where('product_id', $variation->product_id)
            ->where('is_pack', 1)
            ->exists();

        if (!$isPack) {
            return $this->errorResponse('This product is not a pack', 404);
        }

        $product = Product::with(['standard', 'category'])->find($variation->product_id);
        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }

        ScanPackProduct::create([
            'visit_id' => $visit->id,
            'store_id' => $request->store_id,
            'product_id' => $variation->product_id,
            'product_variation_id' => $variation->id,
            'barcode' => $barcode,
        ]);

        return $this->successResponse([
            'is_pack' => true,
            'product' => new ProductResource($product),
        ], 'Pack product found', 200);
    }
    // removePack
    public function removePack(Request $request)
    {
        $visit = Visit::find($request->visit_id);
        if (!$visit) {
            return $this->errorResponse('Visit not found', 404);
        }

        if ((int) $visit->store_id !== (int) $request->store_id) {
            return $this->errorResponse('Store does not match visit', 422);
        }

        $isPack = PackProduct::where('product_id', $request->product_id)
            ->where('is_pack', 1)
            ->exists();

        if (!$isPack) {
            return $this->errorResponse('This product is not a pack', 404);
        }
        $pack = ScanPackProduct::where('product_id', $request->product_id)
            ->where('store_id', $request->store_id)
            ->where('visit_id', $visit->id)->first();
        if (!$pack) {
            return $this->errorResponse('Pack not found', 404);
        }
        $pack->delete();
        return $this->successResponse([], 'Pack removed successfully', 200);
    }

    public function scanPromotion(ScanPackRequest $request)
    {
        $barcode = trim((string) $request->barcode);

        $variation = ProductVariation::where('barcode', $barcode)->first();
        if (!$variation) {
            return $this->errorResponse('Barcode not found', 404);
        }

        $visit = Visit::find($request->visit_id);
        if (!$visit) {
            return $this->errorResponse('Visit not found', 404);
        }

        if ((int) $visit->store_id !== (int) $request->store_id) {
            return $this->errorResponse('Store does not match visit', 422);
        }

        $isPack = PackProduct::where('product_id', $variation->product_id)
            ->where('is_promotion', 1)
            ->exists();

        if (!$isPack) {
            return $this->errorResponse('This product is not a pack', 404);
        }

        $product = Product::with(['standard', 'category'])->find($variation->product_id);
        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }

        ScanPromotionProduct::create([
            'visit_id' => $visit->id,
            'store_id' => $request->store_id,
            'product_id' => $variation->product_id,
            'product_variation_id' => $variation->id,
            'barcode' => $barcode,
        ]);

        return $this->successResponse([
            'is_promotion' => true,
            'product' => new ProductResource($product),
        ], 'Pack product found', 200);
    }

    public function shelfPersentage(ShelfPercentageRequest $request)
    {
        $visit = Visit::find($request->visit_id);
        if (!$visit) {
            return $this->errorResponse('Visit not found', 404);
        }

        if ((int) $visit->store_id !== (int) $request->store_id) {
            return $this->errorResponse('store_id does not match this visit', 422);
        }

        $inputCategories = collect($request->input('categories', []));
        $categoryIds = $inputCategories->pluck('category_id')->unique();
        $categories = Category::whereIn('category_id', $categoryIds)->get()->keyBy('category_id');

        $missing = $categoryIds->diff($categories->keys());
        if ($missing->isNotEmpty()) {
            return $this->errorResponse('Some categories are not valid', 422);
        }

        $parentCategories = $categories->filter(fn($category) => empty($category->parent));
        if ($parentCategories->count() > 1) {
            return $this->errorResponse('Only one parent category can be submitted', 422);
        }

        if ($parentCategories->count() === 1 && $inputCategories->count() > 1) {
            return $this->errorResponse('Parent category must be submitted on its own', 422);
        }

        CategoryShelfPercentage::where('visit_id', $visit->id)
            ->where('store_id', $visit->store_id)
            ->delete();

        $payload = $inputCategories->map(function ($item) use ($categories, $visit) {
            $category = $categories->get($item['category_id']);
            return [
                'visit_id' => $visit->id,
                'store_id' => $visit->store_id,
                'category_id' => $item['category_id'],
                'percentage' => (float) $item['percentage'],
                'is_parent' => empty($category->parent) ? 1 : 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        if (!empty($payload)) {
            CategoryShelfPercentage::insert($payload);
        }

        $saved = CategoryShelfPercentage::where('visit_id', $visit->id)
            ->where('store_id', $visit->store_id)
            ->get();

        return $this->successResponse($saved, 'Shelf percentages saved successfully');
    }
}
