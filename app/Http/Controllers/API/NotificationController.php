<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomNotification;
use App\Models\InvoiceActionNotification;
use App\Models\Notification;
use App\Models\VisitNotification;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        // handle
        $customer = auth('api')->user()->id;
        $unReadNotifications = VisitNotification::where('customer_id', $customer)->whereNull('read_at')->orderBy('created_at', 'desc')->get();
        $message = App::getLocale() == 'en' ? 'Notifications returned Successfully' : 'تمت إعادة الاشعارات بنجاح';
        return $this->successResponse($unReadNotifications, $message);
    }
    // old
    // public function index()
    // {
    //     // handle
    //     $customer = auth('api')->user()->id;
    //     $unReadNotifications = InvoiceActionNotification::where('customer_id', $customer)->whereNull('read_at')->orderBy('created_at', 'desc')->get();
    //     $message = App::getLocale() == 'en' ? 'Notifications returned Successfully' : 'تمت إعادة الاشعارات بنجاح';
    //     return $this->successResponse($unReadNotifications, $message);
    // }

    public function readNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notify_id' => 'required|uuid|exists:visit_notifications,id',
        ]);
        if ($validator->fails()) {
            return $this->errorResponse('validation Error', 422, $validator->errors());
        }
        $customer = auth('api')->user()->id;
        $notification = VisitNotification::where('customer_id', $customer)->where('id', $request->notify_id)->update(['read_at' => now()]);
        return $this->successResponse($notification, App::getLocale() == 'en' ? 'Notification read Successfully' : 'تم قراءة الاشعار بنجاح');
    }
    // old
    // public function readNotification(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'notify_id' => 'required|uuid|exists:invoice_action_notifications,id',
    //     ]);
    //     if ($validator->fails()) {
    //         return $this->errorResponse('validation Error', 422, $validator->errors());
    //     }
    //     $customer = auth('api')->user()->id;
    //     $notification = InvoiceActionNotification::where('customer_id', $customer)->where('id', $request->notify_id)->update(['read_at' => now()]);
    //     return $this->successResponse($notification, App::getLocale() == 'en' ? 'Notification read Successfully' : 'تم قراءة الاشعار بنجاح');
    // }

    public function readAllNotifications()
    {
        $customerId = auth('api')->user()->id;

        $updated = VisitNotification::where('customer_id', $customerId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return $this->successResponse(
            $updated,
            App::getLocale() === 'en' ? 'All notifications marked as read' : 'تم قراءة جميع الإشعارات بنجاح'
        );
    }
    // old
    // public function readAllNotifications()
    // {
    //     $customerId = auth('api')->user()->id;

    //     $updated = InvoiceActionNotification::where('customer_id', $customerId)
    //         ->whereNull('read_at')
    //         ->update(['read_at' => now()]);

    //     return $this->successResponse(
    //         $updated,
    //         App::getLocale() === 'en' ? 'All notifications marked as read' : 'تم قراءة جميع الإشعارات بنجاح'
    //     );
    // }
}
