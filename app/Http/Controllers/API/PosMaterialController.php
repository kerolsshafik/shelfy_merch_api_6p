<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AgentVisits\addAttendanceRequest;
use App\Http\Requests\PosMaterial\PosMaterialRequest;
use App\Http\Requests\PosMaterial\PosRemoveImageRequest;
use App\Http\Resources\AgentVisits\AttendanceResource;
use App\Http\Resources\PosMaterial\PosMaterialResource;
use App\Models\AgentAttendance;
use App\Models\MaterialImage;
use App\Models\PosMaterial;
use App\Traits\ApiResponseTrait;
use App\Traits\ImageHandlingTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                    'image_path'      => $this->saveImage($image, 'pos_material_images'),
                ];
            }
            MaterialImage::insert($images);
        }

        return $this->successResponse(
            new PosMaterialResource($material->load('images'))
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
