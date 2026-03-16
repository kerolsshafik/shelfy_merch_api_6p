<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AgentVisits\addAttendanceRequest;
use App\Http\Resources\AgentVisits\AttendanceResource;
use App\Models\AgentAttendance;
use App\Traits\ApiResponseTrait;
use App\Traits\ImageHandlingTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{

    use ImageHandlingTrait;
    use ApiResponseTrait;
    // Your methods here

    public function addAttendance(addAttendanceRequest $request)
    {

        $time = now()->format('Y-m-d H:i:s');
        $image = $request->file('image');
        $imagePath = $this->saveImage($image, 'attendance_images');

        // visit_id	image	lat	long	start_time	end_time	created_at	updated_at	
        $attendance = AgentAttendance::create([
            'visit_id' => $request->input('visit_id'),
            'image' => $imagePath,
            'lat' => $request->input('lat'),
            'long' => $request->input('long'),
            'start_time' => $time,
            // 'end_time' => $request->input('end_time'),
            'created_at' => $time,
            'updated_at' => $time,

        ]);
        // AttendanceResource
        return $this->successResponse(new AttendanceResource($attendance));
    }
}