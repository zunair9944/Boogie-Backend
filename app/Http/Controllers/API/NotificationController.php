<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Http\Controllers\Controller;
use DB;
use App\Http\Resources\NotificationResource;
use Carbon\Carbon;

class NotificationController extends Controller
{
    public function getList(Request $request)
    {
        $type = isset($request->type) ? $request->type : null;
        if ($type == "markas_read") {
            \DB::table('notifications')
                ->where('notifiable_id', auth()->user()->id)->update([
                    'read' => 1
                ]);
            // $response = [
            //     'status' => true,
            //     'message' => "Notifications Marked as Read"
            // ];
            // return response()->json($response);
        }
        $unreadCount =  \DB::table('notifications')
            ->where('notifiable_id', auth()->user()->id)
            ->where('read', 0)
            ->count();

        $notifications =  \DB::table('notifications')
            ->where('notifiable_id', auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get()
            ->map(function ($notifications) {
                $date = Carbon::parse($notifications->created_at); // now date is a carbon instance
                return [
                    "type" => $notifications->type,
                    "data" => $notifications->data,
                    "read" => $notifications->read,
                    "price" => $notifications->price,
                    "created_at" => $date->diffForHumans(),
                ];
            });
        $data['list'] = $notifications;
        $data['unreadCount'] = $unreadCount;
        $response = [
            'status' => true,
            'data' => $data,
            'message' => "Notification Retrieved Successfully"
        ];
        return response()->json($response);
    }
    public function setReadNotification()
    {
        $notifications =  DB::table('notifications')->where('notifiable_id', auth()->user()->id)->get();
        dd($notifications);
    }
}
