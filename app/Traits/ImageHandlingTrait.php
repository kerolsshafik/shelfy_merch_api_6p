<?php

namespace App\Traits;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
trait ImageHandlingTrait
{

    protected function saveImage($image, $folder)
    {
        $file_extention = $image->getClientOriginalExtension();
        $file_name = time() . uniqid() . '.' . $file_extention;
        $path = asset($folder . '/' . $file_name);
        $image->move($folder, $file_name);
        return $path;
    }


    // public function saveImage($image, string $folder)
    // {
    //     if (is_string($image)) {
    //         // Handle string path
    //         $extension = pathinfo($image, PATHINFO_EXTENSION);
    //         $fileName = time() . uniqid() . '.' . $extension;
    //         return "$folder/$fileName";
    //     } elseif ($image instanceof \Illuminate\Http\UploadedFile) {
    //         // Handle file upload
    //         $extension = $image->getClientOriginalExtension();
    //         $fileName = time() . uniqid() . '.' . $extension;
    //         return $image->storeAs($folder, $fileName);
    //     } elseif ($image instanceof \App\Models\Media) {
    //         // Handle Media model instance
    //         $extension = pathinfo($image->media_path, PATHINFO_EXTENSION);
    //         $fileName = time() . uniqid() . '.' . $extension;
    //         return "$folder/$fileName";
    //     }

    //     throw new \InvalidArgumentException('Invalid image type provided');
    // }

    protected function deleteImage($image, $folder)
    {
        $imageName = basename($image);
        $path = $folder . '/' . $imageName;
        if (File::exists($path)) {
            File::delete($path);
        }
    }
}
