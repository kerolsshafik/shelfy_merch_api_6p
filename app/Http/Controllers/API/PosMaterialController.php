<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\PosMaterial\PosMaterialRequest;
use App\Http\Requests\PosMaterial\PosRemoveImageRequest;
use App\Http\Requests\PosMaterial\PosmStoreImagesRequest;
use App\Http\Resources\PosMaterial\PosMaterialResource;
use App\Http\Resources\PosMaterial\PosmResource;
use App\Models\MaterialImage;
use App\Models\PosMaterial;
use App\Models\Posm;
use App\Models\PosmImage;
use App\Traits\ApiResponseTrait;
use App\Traits\ImageHandlingTrait;

class PosMaterialController extends Controller
{

    use ImageHandlingTrait;
    use ApiResponseTrait;
    // Your methods here

    // public function addMaterial(PosMaterialRequest $request)
    // {
    //     $material = PosMaterial::create([
    //         'visit_id' => $request->input('visit_id'),
    //         'description' => $request->input('description') ?? '',
    //     ]);
    //     $images = [];
    //     if ($request->hasFile('images')) {
    //         foreach ($request->file('images') as $image) {
    //             $images[] = [
    //                 'pos_material_id' => $material->id,
    //                 'image_path' => $this->saveImage($image, 'pos_material_images'),
    //             ];
    //         }
    //     }
    //     MaterialImage::insert($images);

    //     return $this->successResponse(new PosMaterialResource($material->load('images')));
    // }

    public function addMaterial(PosMaterialRequest $request)
    {
        // Update or create the material
        $material = PosMaterial::updateOrCreate(
            ['visit_id' => $request->input('visit_id')], // شرط التحديث
            ['description' => $request->input('description') ?? ''] // البيانات اللي تتعدل/تتسجل
        );

        // لو فيه صور جديدة: احذف القديمة وخزن الجديدة
        if ($request->hasFile('images')) {
            // احذف الصور القديمة من DB


            $images = [];
            foreach ($request->file('images') as $image) {

                $images[] = [
                    'pos_material_id' => $material->id,
                    'image_path' => $this->saveImage($image, 'pos_material_images'),
                ];
            }
            MaterialImage::insert($images);
        }

        return $this->successResponse(
            new PosMaterialResource($material->load('images'))
        );
    }

    public function addStoreImages(PosmStoreImagesRequest $request)
    {
        $posm = Posm::create([
            'visit_id' => $request->input('visit_id'),
            'store_id' => $request->input('store_id'),
            'store_type' => $request->input('store_type'),
        ]);

        $imagesPayload = [];
        foreach ($request->file('images') as $image) {
            $imagesPayload[] = [
                'pos_m_id' => $posm->id,
                'image_path' => $this->saveImage($image, 'posm_store_images'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        PosmImage::insert($imagesPayload);

        return $this->successResponse(
            new PosmResource($posm->load('images'))
        );
    }

    public function removePosImage(PosRemoveImageRequest $request)
    {
        $image = MaterialImage::find($request->id);
        $this->deleteImage($image->image_path, 'pos_material_images');
        $image->delete();
        return $this->successResponse([], 'Image deleted successfully');
    }
}
