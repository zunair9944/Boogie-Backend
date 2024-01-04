<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RideRequest;
use App\Models\RideRequestRating;
use App\Models\Coupon;
use App\Http\Resources\RideRequestResource;
use App\Http\Resources\ComplaintResource;
use Carbon\Carbon;
use App\Models\Payment;
use App\Jobs\NotifyViaMqtt;
use App\Models\Card;
use App\Models\RiderDetail;
use App\Models\RideRequestHistory;
use App\Models\Upload;
use App\Models\User;
use Chatify\Facades\ChatifyMessenger as Chatify;

class RideRequestController extends Controller
{
    public function getList(Request $request)
    {
        $riderequest = RideRequest::where('driver_id', auth()->user()->id)
                                ->select(\DB::raw('DISTINCT DATE(created_at) as date'))
                                ->get();
                $arr = array();
              foreach ($riderequest as $key2 => $value) {
                $data = RideRequest::with('rider')->where('driver_id', auth()->user()->id)
                            ->whereDate('created_at',$value->date)
                            ->get();
                            $arr[$key2]['date'] = \Carbon\Carbon::parse($value->date)->format('M d, Y');
                          foreach ($data as $key => $value2) {
                           $rating = RideRequestRating::where('ride_request_id',$value2->id)
                                                        ->where('driver_id',$value2->driver_id)
                                                        ->first(); 
                            $start_latitude = $value2->start_latitude;
                            $start_longitude = $value2->start_longitude;
                            $end_latitude = $value2->end_latitude;
                            $end_longitude = $value2->end_longitude;
                            $data =  distance($start_latitude, $start_longitude, $end_latitude, $end_longitude);
                            $arr[$key2]['rides'][$key]['destination_addresses']= substr($data['destination_addresses'][0],0,20);
                            $arr[$key2]['rides'][$key]['origin_addresses']= substr($data['origin_addresses'][0],0,15);
                            $arr[$key2]['rides'][$key]['rating']= isset($rating) ? $rating->rating : 0;
                          }
              }
        return response()->json([
            'status' => true,
             'data' => $arr,
              'message' => 'Rides Retreived Successfully'
        ]);
    }

    public function getHistory(Request $request)
    {
        $data = RideRequest::when($request->type == 'rider', function ($q) use ($request) {
            $q->with('driver')->where('rider_id', $request->user_id)->where('status', '!=', 'new_ride_requested');
        })
            ->when($request->type == 'driver', function ($q) use ($request) {
                $q->with('rider')->where('driver_id', $request->user_id)->where('status', '!=', 'new_ride_requested');
            })
            ->orderBy('created_at', 'desc')
            ->get();

            $detail = array();
            foreach ($data as $key => $value) {
                $res = getData($value);
                if(isset($res))
                array_push($detail, getData($value));
            }
            return response()->json([
                'data' => $detail,
                'status' => true,
                'message' => 'History Retrieved Successfully.',
            ]);
    }
    public function cancelRide(Request $request)
    {
        RideRequest::where('id', $request->ride_request_id)
            ->update(['status' => 'cancelled', 'cancel_by' => $request->cancel_by]);
        $rideRequest = RideRequest::where('id', $request->ride_request_id)
            ->select('id', 'cancel_by', 'driver_id', 'rider_id')
            ->with(['rider' => function ($q) {
                return $q->select('id', 'full_name');
            }])
            ->with(['driver' => function ($q) {
                return $q->select('id', 'full_name');
            }])
            ->where('rider_id', auth()->user()->id)
            ->first();

        $data = [
            'rideRequest' => $rideRequest->id,
            'rider_id' => $rideRequest->rider_id,
            'cancel_by' => $rideRequest->cancel_by,
            'driver_id' => $rideRequest->driver_id,
            'rider_name' => $rideRequest->rider->full_name,
            'driver_name' => $rideRequest->driver->full_name,
        ];

        Chatify::push("notifications", 'cancel-ride-request', [
            'data' => $data,
            'message' => "Ride Canceled Successfully"
        ]);

        \DB::table('notifications')->insert([
            'type' => "ride-cancelled",
            'notifiable_id' => $rideRequest->driver_id,
            'notifiable_type' => "ride-cancelled",
            'data' =>  'Ride Canceled Successfully',
            'read' => 0,
            'created_at' => now(),
        ]);
        \DB::table('notifications')->insert([
            'type' => "ride-cancelled",
            'notifiable_id' => $rideRequest->rider_id,
            'notifiable_type' => "ride-cancelled",
            'data' =>  'Ride Canceled Successfully',
            'read' => 0,
            'created_at' => now(),
        ]);

        return response()->json([
            'data' => $data,
            'status' => true,
            'message' => 'Your ride Cancelled Successfully.',
        ]);
    }
    public function arrivedRide(Request $request)
    {
        $ride_request = RideRequest::where('id', $request->id)
            ->where('driver_id', $request->driver_id)
            ->first();
        if ($ride_request  == null)
            return noRideFoundResponse();
        $ride_request->driver->update(['is_available' => 1]);
        //Update Status to Arrived
        $ride_request->update([
            'status' => 'arrived',
            'start_time' => date('h:i:a'),
        ]);
        $riderOrDriverDetail = getData($ride_request);
        // Drive Arrived Push Notification
        Chatify::push("notifications", 'driver-arrived', [
            'data' => $riderOrDriverDetail,
            'message' => "Driver Arrived",
            'status' => true
        ]);

        \DB::table('notifications')->insert([
            'type' => "driver-arrived",
            'notifiable_id' => $request->driver_id,
            'notifiable_type' => "driver-arrived",
            'data' =>  'Driver has Arrived',
            'read' => 0,
            'created_at' => now(),
        ]);

        \DB::table('notifications')->insert([
            'type' => "driver-arrived",
            'notifiable_id' => $ride_request->rider_id,
            'notifiable_type' => "driver-arrived",
            'data' =>  'Driver has Arrived',
            'read' => 0,
            'created_at' => now()
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Driver Arrived',
        ]);
    }

    public function respondRideRequest(Request $request)
    {
        $riderequestcheck = RideRequest::with('service')
                            ->where('id',$request->ride_request_id)
                            ->where('status', 'accepted')
                            // ->orWhere('status','expired')
                            ->first();

        if (isset($riderequestcheck)) {
            $response = [
                'status' => false,
                'message' => "No Active Ride"
            ];
            return response()->json($response);
        }

        $riderequest = RideRequest::with('service')
            ->where('id', $request->ride_request_id)
            ->first();

        if (request()->has('is_accept') && request('is_accept') == 1) {
            $riderequest->driver_id = request('driver_id');
            $riderequest->status = 'accepted';
            $riderequest->max_time_for_find_driver_for_ride_request = 0;
            $riderequest->otp = rand(1000, 9999);
            $riderequest->riderequest_in_driver_id = null;
            $riderequest->riderequest_in_datetime = null;
            $riderequest->save();
            $result = $riderequest;
            $history_data = [
                'history_type'      => 'accepted',
                'ride_request_id'   => $result->id,
                'ride_request'      => $result,
            ];
            \DB::table('notifications')->insert([
                'type' => "ride-accepted",
                'notifiable_id' => $riderequest->rider_id,
                'notifiable_type' => "ride-accepted",
                'data' =>  'Ride Accepted Successfully',
                'read' => 0,
                'created_at' => now(),
            ]);
            \DB::table('notifications')->insert([
                'type' => "ride-accepted",
                'notifiable_id' => $riderequest->driver_id,
                'notifiable_type' => "ride-accepted",
                'data' =>  'Ride Accepted Successfully',
                'read' => 0,
                'created_at' => now(),
            ]);
            saveRideHistory($history_data);
            $response = [
                'status' => true,
                'message' => "Ride Request Accepted"
            ];
            return response()->json($response);
        } else {
            $riderequest->driver_id = request('driver_id');
            $riderequest->status = 'cancelled';
            $riderequest->max_time_for_find_driver_for_ride_request = 0;
            $riderequest->otp = rand(1000, 9999);
            $riderequest->riderequest_in_driver_id = null;
            $riderequest->riderequest_in_datetime = null;
            $riderequest->save();
            $result = $riderequest;
            $history_data = [
                'history_type'      => 'cancelled',
                'ride_request_id'   => $result->id,
                'ride_request'      => $result,
            ];
            saveRideHistory($history_data);
            $riderequest->driver->update(['is_available' => 0]);
            $response = [
                'status' => true,
                'message' => "Ride Request Cancelled"
            ];
            return response()->json($response);
        }
    }

    public function getDetail(Request $request)
    {
        $id = $request->id;
        $riderequest = RideRequest::where('id', $id)->first();

        if ($riderequest == null)
            return noRideFoundResponse();
        $ride_detail = new RideRequestResource($riderequest);

        $ride_history = optional($riderequest)->rideRequestHistory;
        $rider_rating = optional($riderequest)->rideRequestRiderRating();
        $driver_rating = optional($riderequest)->rideRequestDriverRating();

        $current_user = auth()->user();
        if (count($current_user->unreadNotifications) > 0) {
            $current_user->unreadNotifications->where('data.id', $id)->markAsRead();
        }

        $complaint = null;
        if ($current_user->hasRole('driver')) {
            $complaint = optional($riderequest)->rideRequestDriverComplaint();
        }

        if ($current_user->hasRole('rider')) {
            $complaint = optional($riderequest)->rideRequestRiderComplaint();
        }

        $response = [
            'data' => $ride_detail,
            'ride_history' => $ride_history,
            'rider_rating' => $rider_rating,
            'driver_rating' => $driver_rating,
            'complaint' => isset($complaint) ? new ComplaintResource($complaint) : null,
            'payment' => optional($ride_detail)->payment,
            // 'region' => optional($ride_detail)->service_data['region'] 
        ];

        return json_custom_response($response);
    }

    public function completeRideRequest(Request $request)
    {
        // dd("kdkd");
        $id = $request->id;
        $ride_request = RideRequest::where('id', $id)->first();
        // \Log::info('riderequest:'.json_encode($request->all()));
        if ($ride_request == null)
            return noRideFoundResponse();
        
        $start_latitude = $ride_request->start_latitude;
        $start_longitude = $ride_request->start_longitude;
        $end_latitude = $ride_request->end_latitude;
        $end_longitude = $ride_request->end_longitude;
        $data =  distance($start_latitude, $start_longitude, $end_latitude, $end_longitude);
        $calPrice = $ride_request->base_fare + round((int)$data['distance'] * 8);
        // $price =  "RS." .$calPrice ;
        $price =  $calPrice;
        $ride_request->update([
            'status' => 'completed',
            'end_time' => date('h:i:a'),
            'total_amount' => $price,
            'distance' =>  $data['duration'],
        ]);
        // charge Card
        $card_id = RiderDetail::where('rider_id', $ride_request->rider_id)->first();
        $cardId = isset($card) ? $card->card_id : null;
        $price = (int)$calPrice;
         if(isset($cardId))
        chargeCard($card_id,$price);

        Chatify::push("notifications", 'complete-ride-request', [
            'data' => $ride_request,
            'message' => "Ride completed Successfully",
            'status'  => true
        ]);

        \DB::table('notifications')->insert([
            'type' => "ride-completed",
            'notifiable_id' => $ride_request->rider_id ?? 75,
            'notifiable_type' => "ride-completed",
            'data' =>  'Ride Completed Successfully',
            'price' => $price,
            'read' => 0,
            'created_at' => now(),
        ]);

        \DB::table('notifications')->insert([
            'type' => "Ride Completed",
            'notifiable_id' => $ride_request->driver_id ?? 75,
            'notifiable_type' => "ride-completed",
            'data' =>  'Ride Completed Successfully',
            'price' => $price,
            'read' => 0,
            'created_at' => now(),
        ]);
        $deduct_amount = 15;
        $deduct_token  = 15;
        $subscription_renewal = \DB::table('subscription_renewal')
                                   ->where('rider_id', auth()->user()->id)->first();
        if (isset($subscription_renewal)) {
            \DB::table('subscription_renewal')
                ->where('rider_id', auth()->user()->id)
                ->update([
                    'price' => 300 - $deduct_amount,
                    'rider_id' => auth()->user()->id,
                    'tokens' => 150 - $deduct_token,
                ]);
        }
        $data = [
            'price' => $price.'$',
            'date' => $ride_request->created_at->format('M d, Y') . ' at ' . $ride_request->created_at->format('h:i:s')
        ];
        return response()->json([
            'status' => true,
            'data'  => $data,
            'message' => 'Ride Completed Successfully.',
        ]);
    }
    public function calculateRideFares($service, $distance, $duration, $waiting_time, $extra_charges_amount, $coupon)
    {
        // distance price
        $per_minute_drive_charge = 0;

        $per_minute_drive_charge = $duration * $service->per_minute_drive;
        if ($distance > $service->minimum_distance) {
            $distance = $distance - $service->minimum_distance;
        }
        $per_distance_charge = $distance * $service->per_distance;

        $per_minute_waiting_charge = $waiting_time * $service->per_minute_wait;

        $base_fare = $service->base_fare;
        $total_amount = $base_fare + $per_distance_charge + $per_minute_drive_charge + $per_minute_waiting_charge + $extra_charges_amount;

        if ($service->commission_type == 'fixed') {
            $commission = $service->admin_commission + $service->fleet_commission;
            if ($total_amount <= $commission) {
                $total_amount += $commission;
            }
        }
        $subtotal = $total_amount;

        // Check for coupon data
        $discount_amount = 0;
        if ($coupon) {
            if ($coupon->minimum_amount < $total_amount) {
                if ($coupon->discount_type == 'percentage') {
                    $discount_amount = $total_amount * ($coupon->discount / 100);
                } else {
                    $discount_amount = $coupon->discount;
                }
                if ($coupon->maximum_discount > 0 && $discount_amount > $coupon->maximum_discount) {
                    $discount_amount = $coupon->maximum_discount;
                }
                $subtotal = $total_amount - $discount_amount;
            }
        }

        return [
            'base_fare'                 => $base_fare,
            'minimum_fare'              => $service->minimum_fare,
            'base_distance'             => $service->minimum_distance,
            'per_distance'              => $service->per_distance,
            'per_distance_charge'       => $per_distance_charge,
            'per_minute_drive_charge'   => $per_minute_drive_charge,
            'waiting_time'              => $waiting_time,
            'per_minute_waiting_charge' => $per_minute_waiting_charge,
            'subtotal'                  => $subtotal,
            'total_amount'              => $total_amount,
            'extra_charges_amount'      => $extra_charges_amount,
            'coupon_discount'           => $discount_amount,
        ];
    }


    public function rideRating(Request $request)
    {
        $ride_request = RideRequest::where('id', request('ride_request_id'))->first();
        if ($ride_request == null)
            return noRideFoundResponse();
        $data = $request->all();
        $data['rider_id'] = $request->rider_id;
        $data['driver_id'] = $request->driver_id;
        $data['rating_by'] = auth()->user()->user_type;
        $data['comment'] = $request->comment;
        $data['ride_request_id'] = $request->ride_request_id;
        $success = RideRequestRating::updateOrCreate(['id' => $request->id], $data);
        return response()->json([
            'status' => true,
            'data' => $success,
            'message' => 'Rating Successfully.',
        ]);
    }

    public function getRating(Request $request)
    {
        $success = RideRequestRating::when($request->driver_id, function ($query) use ($request) {
            $query->where('driver_id', $request->driver_id);
        })->when($request->driver_id, function ($query) use ($request) {
            $query->where('rider_id', $request->driver_id);
        })->avg('rating');
        return response()->json([
            'status' => true,
            'rating' => $success,
            'message' => 'Rating Successfully.',
        ]);
    }
}
