<?php

namespace App\Traits;

use App\Http\Requests\DriverRequest;
use Illuminate\Http\Request;
use App\Models\RideRequest;
use App\Models\User;
use App\Models\Setting;
use App\Notifications\CommonNotification;
use App\Jobs\NotifyViaMqtt;
use App\Http\Resources\RideRequestResource;
use App\Models\DriversRequest;
use Carbon\Carbon;
use Chatify\Facades\ChatifyMessenger as Chatify;

trait RideRequestTrait
{

    public function acceptDeclinedRideRequest($ride_request, $request_data = null)
    {
        $nearby_drivers = getNearByDrivers($ride_request);

        // on Ride Request Creation create status Record in DB for Driver Notification Sent 
        if ($request_data != "findDriver") {
            foreach ($nearby_drivers as $key => $nearby_driver) {
                // Queue drivers for send request one by one
                DriversRequest::create([
                    'driver_id' =>  $nearby_driver->id,
                    'rider_id' => $ride_request->rider_id,
                    'ride_request_id' => $ride_request->id,
                    'status' =>  "pusherNotSent",
                ]);
                \DB::table('notifications')->insert([
                    'type' => "ride-scheduled",
                    'notifiable_id' => $nearby_driver->id,
                    'notifiable_type' => 'ride-scheduled',
                    'data' =>  "New Ride Request at".$ride_request->created_at->format('M d, Y h:i:s'),
                    'read' => 0,
                    'created_at' => now(),
                 ]);
            }
        }
        //Get driver to sent pusher
        $driver_request_id = DriversRequest::where('ride_request_id', $ride_request->id)
                                             ->where('status', 'pusherNotSent')
                                             ->first();
        if (isset($driver_request_id)) {
            $nearby_driver = $nearby_drivers->where('id', $driver_request_id->driver_id)->first();
            if ($nearby_driver != null) {
                DriversRequest::where([
                    'driver_id' =>  $driver_request_id->driver_id,
                    'ride_request_id' => $ride_request->id,
                ])->update([
                    'status' =>  "pusherSent",
                ]);

                $notification_data =  getDriverRideRequestNotificationData($ride_request, $nearby_driver);
                // Ride Request Notification to Driver 
                Chatify::push("notifications", 'ride-request', [
                    'data' =>  $notification_data
                ]);
                return true;
            }
        } else {
            return false;
        }
    }
}
