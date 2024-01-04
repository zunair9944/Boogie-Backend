<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Events\NewImageEvent;
use App\DataTables\RiderDataTable;
use App\Models\Role;
use App\Http\Requests\RiderRequest;
use App\Models\Card;
use Chatify\Facades\ChatifyMessenger as Chatify;
use App\Models\RiderDetail;
use App\Models\RideRequest;
use Illuminate\Support\Facades\Crypt;
class RiderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(RiderDataTable $dataTable,Request $request)
    {
        // dd($request->approval);
        // $dataTable1 = $dataTable->query($request->approval);
        $pageTitle = __('message.list_form_title',['form' => __('message.rider')] );
        $auth_user = authSession();
        $assets = ['datatable'];
        $button = $auth_user->can('rider add') ? '<a href="'.route('rider.create').'" class="float-right btn btn-sm btn-primary"><i class="fa fa-plus-circle"></i> '.__('message.add_form_title',['form' => __('message.rider')]).'</a>' : '';
        return $dataTable->render('global.datatable', compact('assets','pageTitle','button','auth_user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pageTitle = __('message.add_form_title',[ 'form' => __('message.rider')]);
        $assets = ['phone'];
        return view('rider.form', compact('pageTitle','assets'));
    }

    public function commingDriverStatus(Request $request)
    {
        $rideRequest = RideRequest::where('id',$request->ride_request_id)->first();
        $nearby_driver = User::where('id',$rideRequest->driver_id)->first();
        $riderPickup =  distance($request->latitude,$request->longitude,$nearby_driver->latitude,$nearby_driver->longitude);
        return response()->json($riderPickup);
    }

    public function getRiderCards()
    {
        $new = array();
          $riderDetail =Card::where('rider_id',auth()->user()->id)->get();
        
           $newcards = array();
           foreach ($riderDetail as $key => $value) {
                   $new["id"]= $value->id;
                   $new["billing_zip"]= $value->billing_zip;
                   $new["card_holder_name"]=$value->card_holder_name;
                   $new["cvc"]=$value->cvc;
                   $new["expiration_date"]= $value->expiration_date;
                   $new["card_number"]= getTruncatedCCNumber($value->card_number);
                   $new["default"]= $value->default;
                   array_push($newcards,$new);
                }
      
            
          if(isset($newcards))
          {
            return response()->json([
                  'status' => true,
                  'data' => $newcards,
                  'message' => 'Data retrieved Successfully'
                ]);
          }
          else
          {

            return response()->json([
                    'status' => true,
                    'data' => $newcards,
                    'message' => 'No Card Information',
            ]);
          }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
     
     public function store(RiderRequest $request)
        {
            $request['password'] = bcrypt($request->password);
            $request['username'] = $request->username ?? stristr($request->email, "@", true) . rand(100,1000);
            $request['display_name'] = $request->first_name.' '. $request->last_name;
            $request['user_type'] = 'rider';
            $user = User::create($request->all());
        
            // $imageData = uploadMediaFile($user, $request->profile_image, 'profile_image');
           $imageData =  Chatify::push("notifications", $request->profile_image, 'profile_image',  [
                    'data' =>  $notification_data
                ]);
            // Dispatch the event
            // event(new NewImageEvent($user->id, $imageData));
        
            $user->assignRole('rider');
            
            return redirect()->route('rider.index')->withSuccess(__('message.save_form', ['form' => __('message.rider')]));
        }


    // public function store(RiderRequest $request)
    // {
    //     $request['password'] = bcrypt($request->password);

    //     $request['username'] = $request->username ?? stristr($request->email, "@", true) . rand(100,1000);
    //     $request['display_name'] = $request->first_name.' '. $request->last_name;
    //     $request['user_type'] = 'rider';
    //     $user = User::create($request->all());

    //     uploadMediaFile($user,$request->profile_image, 'profile_image');
        
    //     // Dispatch the event
    //     event(new NewImageEvent($imageData));

    //     $user->assignRole('rider');
        
    //     return redirect()->route('rider.index')->withSuccess(__('message.save_form', ['form' => __('message.rider')]));
    // }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pageTitle = __('message.add_form_title',[ 'form' => __('message.rider')]);
        $data = User::with('roles')->findOrFail($id);

        $profileImage = getSingleMedia($data, 'profile_image');

        return view('rider.show', compact('data', 'profileImage'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pageTitle = __('message.update_form_title',[ 'form' => __('message.rider')]);
        $data = User::findOrFail($id);

        $profileImage = getSingleMedia($data, 'profile_image');
        $assets = ['phone'];
        return view('rider.form', compact('data', 'pageTitle', 'id', 'profileImage', 'assets'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RiderRequest $request, $id)
{
    $user = User::findOrFail($id);

    $request['password'] = $request->password != '' ? bcrypt($request->password) : $user->password;
    $request['display_name'] = $request->first_name.' '. $request->last_name;
    
    // Update user data
    $user->fill($request->all())->update();

    // Save user image
    if (isset($request->profile_image) && $request->profile_image != null) {
        $user->clearMediaCollection('profile_image');
        $user->addMediaFromRequest('profile_image')->toMediaCollection('profile_image');
    }

    // $imageData = getSingleMedia($user, 'profile_image');
    $imageData = getSingleMedia($user, 'profile_image');
    
    // Push the image to the chat channel
    $notification_data = ['userId'=> $user->id, 'imageData' => $imageData ];
    Chatify::push("notifications", 'profile_image', [
                    'data' =>  $notification_data
                ]);
    // Chatify::push("notifications", $request->profile_image, 'profile_image', [
    //     'data' => $imageData
    // ]);

    // Dispatch the event
    event(new NewImageEvent($user->id, $imageData));

    if (auth()->check()) {
        return redirect()->route('rider.index')->withSuccess(__('message.update_form',['form' => __('message.rider')]));
    }

    return redirect()->back()->withSuccess(__('message.update_form',['form' => __('message.rider') ] ));
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(env('APP_DEMO')){
            $message = __('message.demo_permission_denied');
            if(request()->ajax()) {
                return response()->json(['status' => true, 'message' => $message ]);
            }
            return redirect()->route('rider.index')->withErrors($message);
        }
        $user = User::findOrFail($id);
        $status = 'errors';
        $message = __('message.not_found_entry', ['name' => __('message.rider')]);

        if($user!='') {
            $user->delete();
            $status = 'success';
            $message = __('message.delete_form', ['form' => __('message.rider')]);
        }

        if(request()->ajax()) {
            return response()->json(['status' => true, 'message' => $message ]);
        }

        return redirect()->back()->with($status,$message);
    }
}
