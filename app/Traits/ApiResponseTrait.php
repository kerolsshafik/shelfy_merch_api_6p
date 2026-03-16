<?php

namespace App\Traits;

trait ApiResponseTrait
{
    /**
     * Success response method.
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($data, $message = "Request succeeded", $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Error response method.
     *
     * @param string $message
     * @param int $code
     * @param mixed $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse($message = "Request failed", $code = 400, $data = null)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    // protected function saveImage($image, $folder)
    // {
    //     $file_extention = $image->getClientOriginalExtension();
    //     $file_name = time() . uniqid() . '.' . $file_extention;
    //     $path = asset($folder . '/' . $file_name);
    //     $image->move($folder, $file_name);
    //     return $path;

    // }
    // protected function deleteImage($image_path)
    // {
    //     if (file_exists($image_path)) {
    //         unlink($image_path);
    //     }
    //     return true;
    // }
}
