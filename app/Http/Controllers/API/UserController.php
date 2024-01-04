<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserOtp;
use Illuminate\Support\Facades\Cache;
use App\Models\DriverDocument;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\DriverResource;
use Illuminate\Support\Facades\Password;
use App\Models\AppSetting;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\DriverRequest;
use App\Models\RiderDetail;
use App\Models\Music;
use App\Models\DriverPreference;
use App\Models\UserDetail;
use Twilio;
use Twilio\Rest\Client;
use App\Http\Requests\ForgetPasswordRequest;
use App\Models\AboutMeDetail;
use App\Models\Card;
use App\Models\RideRequest;
use App\Models\Upload;
use Illuminate\Support\Facades\Validator;
use DB;

use Session;

class UserController extends Controller
{
    public function subscription(Request $request)
    {
        $amount = 300;
        chargeCard($request->id, $amount);
        $subscription_renewal = \DB::table('subscription_renewal')
            ->where('rider_id', auth()->user()->id)
            ->first();
        if (isset($subscription_renewal)) {
            \DB::table('subscription_renewal')
                ->where('rider_id', auth()->user()->id)
                ->update([
                    'price' => 300,
                    'rider_id' => auth()->user()->id,
                    'tokens' => 150,
                ]);
        } else {
            \DB::table('subscription_renewal')
                ->insert([
                    'price' => 300,
                    'rider_id' => auth()->user()->id,
                    'tokens' => 150,
                ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Charged Successully'
        ]);
    }
    public function getBalance(Request $request)
    {
        $subscription = \DB::table('subscription_renewal')
            ->select('price')
            ->where('rider_id', auth()->user()->id)
            ->first();

        $subscription = $subscription == null ? ['price' => 0] : $subscription;
        return response()->json([
            'status' => true,
            'data'  =>  $subscription,
            'message' => 'Detail Retrieved Successfully'
        ]);
    }
    function purchaseToken(Request $request)
{
    $record = \DB::table('subscription_renewal')
        ->where('rider_id', auth()->user()->id)
        ->first();
// dd( auth()->user()->id);
    if (isset($record)) {
        if ($record->price < $request->price) {
            return response()->json([
                'status' => false,
                'message' => 'Not Enough Amount to Purchase'
            ]);
        }

        // Check if there are enough tokens to be deducted
        if ($record->tokens < $request->token) {
            return response()->json([
                'status' => false,
                'message' => 'Not Enough Tokens to Purchase'
            ]);
        }

        // Update the subscription renewal record
        $price = $record->price - $request->price;
        $token = $record->tokens - $request->token;
        \DB::table('subscription_renewal')
            ->where('id', $record->id) // Assuming there's a primary key 'id' in the table
            ->update([
                'price' => $price,
                'tokens' => $token,
            ]);

        // Create token purchase history
        \DB::table('purchase_history')
            ->insert([
                'price' => $request->price,
                'user_id' => auth()->user()->id,
                'token' => $request->token,
            ]);
    } else {
        return response()->json([
            'status' => false,
            'message' => 'No Subscription Exists'
        ]);
    }

    return response()->json([
        'status' => true,
        'message' => 'Token Purchased Successfully'
    ]);
}
    public function pushNotification()
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://b24c9c42-6e2f-481b-a9ef-ab186c616955.pushnotifications.pusher.com/publish_api/v1/instances/b24c9c42-6e2f-481b-a9ef-ab186c616955/publishes',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{"interests":["hello"],"fcm":{"notification":{"title":"Hello", "body":"Hello, world!"}}}',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer DF68A4FDFE67CFE356933BAFF0DF8CE0F4568956FC0E9337FCA338865E75A14F'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        echo $response;
    }
    
    
    public function isonline()
{
    if (auth()->check()) {
        User::where('id', auth()->user()->id)->update(['is_online' => 1]);
        return response()->json([
            'status' => true,
            'message' => 'User is Online and Available',
        ]);
    } else {
        // User is logged out, change status to offline
        User::where('id', auth()->user()->id)->update(['is_online' => 0]);
        return response()->json([
            'status' => true,
            'message' => 'User is Offline',
        ]);
    }
}


    // public function isonline()
    // {
    //     User::where('id', auth()->user()->id)->update(['is_online' => 1]);
    //     return response()->json([
    //         'status' => true,
    //         'message' => 'User is Online and Available',
    //     ]);
    // }
    
 public function profileImage(Request $request)
{
    $upload = Upload::latest()->first();

    if ($upload) {
        // Update the user's profile image ID only if an upload record exists
        User::where('id', auth()->user()->id)
            ->update(['profile_image_id' => $upload->id]);

        // Update the is_profile_image_uploaded field to true
        User::where('id', auth()->user()->id)
            ->update(['is_profile_image_uploaded' => true]);
    }

    $user = auth()->user(); // Get the authenticated user

    $isProfileImageUploaded = $user->is_profile_image_uploaded; // Retrieve the value of is_profile_image_uploaded from the user model

    $imagePath = $upload ? str_replace("public/", "", $upload->file_name) : null;

    return response()->json([
        'status' => true,
        'data' => [
            'link' => $imagePath ? asset($imagePath) : null,
            'is_profile_image_uploaded' => $isProfileImageUploaded,
        ],
        'message' => 'Data Retrieved Successfully',
    ]);
}


// public function profileImage(Request $request)
// {
//     $upload = Upload::latest()->first();
//     $user = auth()->user();

//     // Update profile_image_id only if an upload record exists
//     if ($upload) {
//         $user->update(['profile_image_id' => $upload->id]);
//     }

//     // Check if user has a profile image
//     $isProfileImageUploaded = !is_null($user->profile_image_id);

//     $imagePath = $upload ? str_replace("public/", "", $upload->file_name) : null;

//     return response()->json([
//         'status' => true,
//         'data' => [
//             'link' => $imagePath ? asset($imagePath) : null,
//             'is_profile_image_uploaded' => $isProfileImageUploaded,
//         ],
//         'message' => 'Data Retrieved Successfully',
//     ]);
// }





//     public function profileImage(Request $request)
// {
//   $imageId = Upload::latest()->first();
//     // Assuming you are receiving an image ID in the request
//     $imageId = $request->input('profile_image_id');

//     if (!empty($imageId)) {
//         // Update the user's profile_image_id with the provided image ID
//         User::where('id', auth()->user()->id)
//             ->update(['profile_image_id' => $imageId]);

//         // Update the is_profile_image_uploaded field to true
//         User::where('id', auth()->user()->id)
//             ->update(['is_profile_image_uploaded' => true]);
//     }

//     // Fetch the user's latest profile image by using the profile_image_id
//     $user = User::find(auth()->user()->id);
//     $image = $user->profileImage ? asset($user->profileImage->file_name) : null;

//     return response()->json([
//         'status' => true,
//         'data' => ['link' => $image],
//         'message' => 'Data Retrieved Successfully',
//     ]);
// }

public function getUserDetail()
{
    $user = User::with(['riderDetail', 'cards', 'driverDetail', 'aboutMeDetail'])
        ->without('riderRating', 'driverRating')
        ->where('id', auth()->user()->id)
        ->first()->toArray();

    $userIdentity = Upload::where('id', $user['userIdentity_id'])->first();
    $driverLicence = Upload::where('id', $user['driver_licence'])->first();
    $businessLicence = Upload::where('id', $user['business_licence'])->first();
    $vehicleRegistrationDocument = Upload::where('id', $user['vehicle_registration_document'])->first();
    $nvBusinessImage = Upload::where('id', $user['nv_business_image'])->first(); // Added this line
    $vehicleRegistrationDocument2 = Upload::where('id', $user['vehicle_registration_document_2'])->first();
    $insuranceInspection = Upload::where('id', $user['insurance_inspection'])->first();

    $userIdentityPath = isset($userIdentity) ? str_replace("public/", "", $userIdentity->file_name) : null;
    $driverLicencePath = isset($driverLicence) ? str_replace("public/", "", $driverLicence->file_name) : null;
    $businessLicencePath = isset($businessLicence) ? str_replace("public/", "", $businessLicence->file_name) : null;
    $vehicleRegistrationDocumentPath = isset($vehicleRegistrationDocument) ? str_replace("public/", "", $vehicleRegistrationDocument->file_name) : null;
    $nvBusinessImagePath = isset($nvBusinessImage) ? str_replace("public/", "", $nvBusinessImage->file_name) : null; // Added this line
    $vehicleRegistrationDocument2Path = isset($vehicleRegistrationDocument2) ? str_replace("public/", "", $vehicleRegistrationDocument2->file_name) : null;
    $insuranceInspectionPath = isset($insuranceInspection) ? str_replace("public/", "", $insuranceInspection->file_name) : null;

    $data['userIdentity'] = isset($userIdentityPath) ? asset($userIdentityPath) : null;
    $data['driver_licence'] = isset($driverLicence) ? asset($driverLicencePath) : null;
    $data['business_licence'] = isset($businessLicence) ? asset($businessLicencePath) : null;
    $data['vehicle_registration_document'] = isset($vehicleRegistrationDocument) ? asset($vehicleRegistrationDocumentPath) : null;
    $data['nv_business_image'] = isset($nvBusinessImage) ? asset($nvBusinessImagePath) : null; // Added this line
    $data['vehicle_registration_document_2'] = isset($vehicleRegistrationDocument2) ? asset($vehicleRegistrationDocument2Path) : null;
    $data['insurance_inspection'] = isset($insuranceInspection) ? asset($insuranceInspectionPath) : null;
    $data['userIdentity_id'] = isset($userIdentity) ? $userIdentity->id : null;
    $data['driver_licence_id'] = isset($driverLicence) ? $driverLicence->id : null;
    $data['nv_business_image_id'] = isset($nvBusinessImage) ? $nvBusinessImage->id : null;
    $data['business_licence_id'] = isset($businessLicence) ? $businessLicence->id : null;
    $data['vehicle_registration_document_id'] = isset($vehicleRegistrationDocument) ? $vehicleRegistrationDocument->id : null;
    $data['vehicle_registration_document_2_id'] = isset($vehicleRegistrationDocument2) ? $vehicleRegistrationDocument2->id : null;
    $data['insurance_inspection_id'] = isset($insuranceInspection) ? $insuranceInspection->id : null;

    $user = array_merge($user, $data);

    return response()->json([
        'status' => true,
        'data' => $user,
        'message' => 'Data Retrieved Successfully',
    ]);
}


    // public function getUserDetail()
    // {
    //     $user = User::with(['riderDetail', 'cards', 'driverDetail','aboutMeDetail'])
    //         ->without('riderRating', 'driverRating')
    //         ->where('id', auth()->user()->id)
    //         ->first()->toArray();
    //         // dd($user);
    //     $userIdentity =  Upload::where('id', $user['userIdentity_id'])->first();
    //     $driver_licence =  Upload::where('id', $user['driver_licence'])->first();
    //     $business_licence =  Upload::where('id', $user['business_licence'])->first();
    //     $vehicle_registration_document =  Upload::where('id', $user['vehicle_registration_document'])->first();
    //     $nv_business_image =  Upload::where('id', $user['nv_business_image'])->first();
    //     $vehicle_registration_document_2 =  Upload::where('id', $user['vehicle_registration_document_2'])->first();
    //     $insurance_inspection =  Upload::where('id', $user['insurance_inspection'])->first();

    //     $userIdentity_path =  isset($userIdentity) ? str_replace("public/", "", $userIdentity->file_name) : null;
    //     $driver_licence_path =  isset($driver_licence) ? str_replace("public/", "", $driver_licence->file_name) : null;
    //     $business_licence_path = isset($business_licence) ?  str_replace("public/", "", $business_licence->file_name) : null;
    //     $vehicle_registration_document_path = isset($vehicle_registration_document) ?  str_replace("public/", "", $vehicle_registration_document->file_name) : null;
    //     $nv_business_image_path = isset($nv_business_image) ?  str_replace("public/", "", $nv_business_image->file_name) : null;
    //     $vehicle_registration_document2_path = isset($vehicle_registration_document_2) ?  str_replace("public/", "", $vehicle_registration_document_2->file_name) : null;
    //     $insurance_inspection_path = isset($insurance_inspection) ?  str_replace("public/", "", $insurance_inspection->file_name) : null;
    //     $data['userIdentity'] = isset($userIdentity_path) ? asset($userIdentity_path) : null;
    //     $data['driver_licence'] = isset($driver_licence) ? asset($driver_licence_path) : null;
    //     $data['business_licence'] = isset($business_licence) ? asset($business_licence_path) : null;
    //     $data['vehicle_registration_document'] = isset($vehicle_registration_document) ? asset($vehicle_registration_document_path) : null;
    //     $data['nv_business_image'] = isset($nv_business_image) ? asset($nv_business_image_path) : null;
    //     $data['vehicle_registration_document_2'] = isset($vehicle_registration_document_2) ? asset($vehicle_registration_document2_path) : null;
    //     $data['insurance_inspection'] = isset($insurance_inspection) ? asset($insurance_inspection_path) : null;
    //     $data['userIdentity_id'] = isset($userIdentity) ?  $userIdentity->id : null;
    //     $data['driver_licence_id'] = isset($driver_licence) ? $driver_licence->id : null;
    //     $data['business_licence_id'] = isset($business_licence) ? $business_licence->id : null;
    //     $data['vehicle_registration_document_id'] = isset($vehicle_registration_document) ? $vehicle_registration_document->id : null;
    //     $data['vehicle_registration_document_2_id'] = isset($vehicle_registration_document_2) ? $vehicle_registration_document_2->id : null;
    //     $data['insurance_inspection_id'] = isset($insurance_inspection) ? $insurance_inspection->id : null;
    //     $user = array_merge($user, $data);
    //     return response()->json([
    //         'status' => true,
    //         'data'  => $user,
    //         'message' => 'Data Retrieved Successfully',
    //     ]);
    // }
    
    
//     public function getUserDetail()
// {
//     $user = User::with(['riderDetail', 'cards', 'driverDetail','aboutMeDetail'])
//         ->without('riderRating', 'driverRating')
//         ->where('id', auth()->user()->id)
//         ->first()->toArray();

//     $userIdentity =  Upload::where('id', $user['userIdentity_id'])->first();
//     $driver_licence =  Upload::where('id', $user['driver_licence'])->first();
//     $business_licence =  Upload::where('id', $user['business_licence'])->first();
//     $vehicle_registration_document =  Upload::where('id', $user['vehicle_registration_document'])->first();
//     $nv_business_image =  Upload::where('id', $user['nv_business_image'])->first();
//     $vehicle_registration_document_2 =  Upload::where('id', $user['vehicle_registration_document_2'])->first();
//     $insurance_inspection =  Upload::where('id', $user['insurance_inspection'])->first();

//     $data['userIdentity'] = isset($userIdentity) ? Storage::url($userIdentity->file_name) : null;
//     $data['driver_licence'] = isset($driver_licence) ? Storage::url($driver_licence->file_name) : null;
//     $data['business_licence'] = isset($business_licence) ? Storage::url($business_licence->file_name) : null;
//     $data['vehicle_registration_document'] = isset($vehicle_registration_document) ? Storage::url($vehicle_registration_document->file_name) : null;
//     $data['nv_business_image'] = isset($nv_business_image) ? Storage::url($nv_business_image->file_name) : null;
//     $data['vehicle_registration_document_2'] = isset($vehicle_registration_document_2) ? Storage::url($vehicle_registration_document_2->file_name) : null;
//     $data['insurance_inspection'] = isset($insurance_inspection) ? Storage::url($insurance_inspection->file_name) : null;

//     $data['userIdentity_id'] = isset($userIdentity) ?  $userIdentity->id : null;
//     $data['driver_licence_id'] = isset($driver_licence) ? $driver_licence->id : null;
//     $data['business_licence_id'] = isset($business_licence) ? $business_licence->id : null;
//     $data['vehicle_registration_document_id'] = isset($vehicle_registration_document) ? $vehicle_registration_document->id : null;
//     $data['vehicle_registration_document_2_id'] = isset($vehicle_registration_document_2) ? $vehicle_registration_document_2->id : null;
//     $data['insurance_inspection_id'] = isset($insurance_inspection) ? $insurance_inspection->id : null;

//     $user = array_merge($user, $data);

//     return response()->json([
//         'status' => true,
//         'data'  => $user,
//         'message' => 'Data Retrieved Successfully',
//     ]);
// }

    public function riderUpdate(Request $request)
    {
        // $input = $request->all();

        $cards = encrypt(json_decode($request->cards));
        // $parts = explode(" ", $request->name);
        // if (count($parts) > 1) {
        //     $lastname = array_pop($parts);
        //     $firstname = implode(" ", $parts);
        // } else {
        //     $firstname = $request->name;
        //     $lastname = " ";
        // }
        $input['first_name'] = $request->first_name;
        $input['last_name'] = $request->last_name;
        $input['full_name'] = $request->full_name;
        $input['contact_number'] = $request->contact_number;

        $input['userIdentity_id'] = $request->userIdentity;
        $input['user_type'] = isset($input['user_type']) ? $input['user_type'] : 'rider';
        // $input['password'] = Hash::make($password);

        if (in_array($input['user_type'], ['driver'])) {
            $input['status'] = isset($input['status']) ? $input['status'] : 'pending';
        }
        $input['display_name'] = $input['first_name'] . " " . $input['last_name'];

            $user = User::where('id', $request->id)->update($input);

            //  Save Rider Detail
            // if (isset($request->userIdentity) && $request->userIdentity != null) {
            //     $img = explode(",", $request->userIdentity)[1];
            //     $base64data = str_replace(',', '', $img);
            //     $user->clearMediaCollection('userIdentity');
            //     $user->addMediaFromBase64($base64data)
            //         ->usingFileName('userIdentity.jpg')
            //         ->toMediaCollection('userIdentity');
            // }
            // $user->getMedia('document_image');

            $user = User::where('id', $request->id)->first();
            $riderDetail =  RiderDetail::where('rider_id', $request->id)->updateOrCreate([
                'rider_id' => $request->id,
                'card_id' => 0,
                'driver_preference' => $request->driver_prefrences,
                'music_choice' => $request->music,
                'image' => getSingleMedia($user, 'userIdentity', null),
            ]);
            // dd( $request->obsessed_with);
            $about_detail = AboutMeDetail::where('user_id', $request->id)->first();
            if(isset($about_detail))
            {
                 AboutMeDetail::where('user_id', $request->id)->update([
                'user_id' => $request->id,
                'fun_fact' => $request->fun_fact ?? null,
                'next_place' => $request->next_place ?? null,
                'movie'  => $request->movie ?? null,
                'fear' => $request->fear ?? null,
                'live_without' => $request->live_without ?? null,
                'obessed_with' => $request->obsessed_with ?? null,
                'next_time' => $request->next_time ?? null,
                // 'image' => getSingleMedia($user, 'userIdentity', null),
            ]);
            }
            else
            {
                 AboutMeDetail::create([
                'user_id' => $request->id,
                'fun_fact' => $request->fun_fact ?? null,
                'next_place' => $request->next_place ?? null,
                'movie'  => $request->movie ?? null,
                'fear' => $request->fear ?? null,
                'live_without' => $request->live_without ?? null,
                'obessed_with' => $request->obsessed_with ?? null,
                'next_time' => $request->next_time ?? null,
                // 'image' => getSingleMedia($user, 'userIdentity', null),
            ]);
            }
           
            // $cards = json_decode(json_decode($request->cards));
             $cards = json_decode($request->cards);
            $riderDetail =  RiderDetail::where('rider_id', $request->id)->first();
            foreach ($cards as $key => $value) {
                $card = array_merge((array)$value, ['rider_id' => $user->id, 'card_number' => $value->card_number]);
                if (isset($value->id))
                    $savedCard = Card::where('id', $value->id)->first();
                else
                    $savedCard = Card::create($card);

                if (isset($savedCard) && $savedCard->default == 1) {
                    $riderDetail->update(['card_id' => $savedCard->id]);
                }
            }
            $user->assignRole($input['user_type']);
            $user->api_token = $user->createToken('auth_token')->plainTextToken;
            // $user->profile_image = getSingleMedia($user, 'profile_image', null);
        $user = User::where('id', $user->id)->with(['riderDetail', 'aboutMeDetail'])->first();
        $user->profile_image = getImagePath($user);
        $message = __('message.save_form', ['form' => __('message.' . $input['user_type'])]);
        $response = [
            'status' => true,
            'message' => $message,
            'data' => $user
        ];
        return json_custom_response($response);
        // });
    }
    
//     public function driverUpdate(Request $request)
// {
//     $input = $request->all();
//     $input['first_name'] = $request->first_name;
//     $input['last_name'] = $request->last_name;
//     $input['email'] = $request->email;
//     $input['contact_number'] = $request->contact_number;
//     $input['address'] = $request->address;
//     $input['user_type'] = isset($request->user_type) ? $request->user_type : 'driver';
//     $input['status'] = "active";
//     $input['display_name'] = $request->first_name . " " . $request->last_name;
//     $input['is_available'] = 1;
//     $input['driver_licence'] = $request->input('driver_licence', null);
//     $input['business_licence'] = $request->input('business_licence', null);
//     $input['vehicle_registration_document'] = $request->input('vehicle_registration_document', null);
//     $input['nv_business_image'] = $request->input('nv_business_image', null);
//     $input['vehicle_registration_document_2'] = $request->input('vehicle_registration_document_2', null);
//     $input['insurance_inspection'] = $request->input('insurance_inspection', null);

//     // Set flags based on whether image IDs are stored
//     $input['is_driver_licence'] = !is_null($input['driver_licence']);
//     $input['is_business_licence'] = !is_null($input['business_licence']);
//     $input['is_vehicle_registration_document'] = !is_null($input['vehicle_registration_document']);
//     $input['is_nv_business_image'] = !is_null($input['nv_business_image']);
//     $input['is_vehicle_registration_document_2'] = !is_null($input['vehicle_registration_document_2']);
//     $input['is_insurance_inspection'] = !is_null($input['insurance_inspection']);

//     User::where('id', $request->id)->update($input);
//     $user = User::where('id', $request->id)->first();

//     // Rest of your code remains unchanged...

//     $user->api_token = $user->createToken('auth_token')->plainTextToken;

//     // Set image paths and other details

//     $response = [
//         'message' => "Updated Successfully",
//         'data' => $user,
//         'status' => true
//     ];
//     return json_custom_response($response);
// }

    public function driverUpdate(Request $request)
    {
        // $input = $request->all();
        // $parts = explode(" ", $request->full_name);
        // if (count($parts) > 1) {
        //     $lastname = array_pop($parts);
        //     $firstname = implode(" ", $parts);
        // } else {
        //     $firstname = $request->full_name;
        //     $lastname = " ";
        // }
        // $input['full_name'] = $request->full_name;
        $input['first_name'] = $request->first_name;
        $input['last_name'] = $request->last_name;
        // $input['last_name'] = $lastname;
        $input['email'] = $request->email;
        $input['contact_number'] = $request->contact_number;
        $input['address'] = $request->address;
        $input['user_type'] = isset($request->user_type) ? $request->user_type : 'driver';
        // $input['password'] = Hash::make($driverdetail->password);
        // $input['status'] = isset($input['status']) ? $input['status']: 'pending';
        $input['status'] = "active";
        $input['display_name'] = $request->first_name . " " . $request->last_name;
        // $input['display_name'] = $request->display_name;
        $input['is_available'] = 1;
        $input['driver_licence'] = $request->driver_licence;
        $input['driver_licence'] = $request->input('driver_licence', null);
        $input['business_licence'] = $request->input('business_licence', null);
        $input['vehicle_registration_document'] = $request->input('vehicle_registration_document', null);
        $input['nv_business_image'] = $request->input('nv_business_image', null);
        $input['vehicle_registration_document_2'] = $request->input('vehicle_registration_document_2', null);
        $input['insurance_inspection'] = $request->input('insurance_inspection', null);
        // $input['business_licence'] = $request->business_licence;
        // $input['vehicle_registration_document'] = $request->vehicle_registration_document;
        // $input['nv_business_image'] = $request->nv_business_image;
        // $input['vehicle_registration_document_2'] = $request->vehicle_registration_document_2;
        // $input['insurance_inspection'] = $request->insurance_inspection;
        
        // Set flags based on whether image IDs are stored
        $input['is_driver_licence'] = !is_null($input['driver_licence']);
        $input['is_business_licence'] = !is_null($input['business_licence']);
        $input['is_vehicle_registration_document'] = !is_null($input['vehicle_registration_document']);
        $input['is_nv_business_image'] = !is_null($input['nv_business_image']);
        $input['is_vehicle_registration_document_2'] = !is_null($input['vehicle_registration_document_2']);
        $input['is_insurance_inspection'] = !is_null($input['insurance_inspection']);
        User::where('id', $request->id)->update($input);
        $user = User::where('id', $request->id)->first();
        // $user->assignRole($request->user_type);
        if (isset($user->aboutMeDetail)) {
            $user->aboutMeDetail()->update([
                // 'user_id' => $request->id,
                'fun_fact' => $request->fun_fact ?? null,
                'next_place' => $request->next_place ?? null,
                'movie'  => $request->movie ?? null,
                'fear' => $request->fear ?? null,
                'live_without' => $request->live_without ?? null,
                'obessed_with' => $request->obsessed_with ?? null,
                'next_time' => $request->next_time ?? null,
                // 'image' => getSingleMedia($user, 'userIdentity', null),
            ]);
        } else {
            $user->aboutMeDetail()->create([
                // 'user_id' => $request->id,
                'fun_fact' => $request->fun_fact ?? null,
                'next_place' => $request->next_place ?? null,
                'movie'  => $request->movie ?? null,
                'fear' => $request->fear ?? null,
                'live_without' => $request->live_without ?? null,
                'obessed_with' => $request->obsessed_with ?? null,
                'next_time' => $request->next_time ?? null,
                // 'image' => getSingleMedia($user, 'userIdentity', null),
            ]);
        }
        if (isset($user->userDetail)) {
            $user->userDetail()->update([
                'city'  =>  $request->city,
                'state'  =>  $request->state,
                'zip'  =>  $request->zip,
                'home_address' =>  $request->address,
                'car_category_id' => $request->car_category_id,
                'car_model' => $request->car_model,
                'car_plate_number' => $request->car_plate_number,
                'car_made_in' => $request->car_made_in,
            ]);
        } else {
            $user->userDetail()->create([
                'city'  =>  $request->city,
                'state'  =>  $request->state,
                'zip'  =>  $request->zip,
                'home_address' =>  $request->address,
                'car_category_id' => $request->car_category_id,
                'car_model' => $request->car_model,
                'car_plate_number' => $request->car_plate_number,
                'car_made_in' => $request->car_made_in,
            ]);
        }

        $userDetail =  UserDetail::where('user_id', $request->id)->first();
        $cards = json_decode($request->cards);
        // $cards = json_decode(json_decode($request->cards));
        foreach ($cards as $key => $value) {
            $card = array_merge((array)$value, ['rider_id' => $user->id, 'card_number' => $value->card_number]);
            if (isset($value->id)) {
                $savedCard = Card::where('id', $value->id)->first();
                $savedCard->update(['default' => $value->default]);
            } else {
                $savedCard = Card::create($card);
            }

            if (isset($savedCard, $userDetail) && $savedCard->default == 1) {
                $userDetail->update(['card_id' => $savedCard->id]);
            }
        }
        $user->api_token = $user->createToken('auth_token')->plainTextToken;
        $driver_licence =  Upload::where('id', $user->driver_licence)->first();
        $business_licence =  Upload::where('id', $user->business_licence)->first();
        $vehicle_registration_document =  Upload::where('id', $user->vehicle_registration_document)->first();
        $nv_business_image =  Upload::where('id', $user->nv_business_image)->first();
        $vehicle_registration_document_2 =  Upload::where('id', $user->vehicle_registration_document_2)->first();
        $insurance_inspection =  Upload::where('id', $user->insurance_inspection)->first();
        $user->driver_licence_image = isset($driver_licence) ? asset($driver_licence->file_name) : null;
        $user->business_licence_image = isset($business_licence) ? asset($business_licence->file_name) : null;
        $user->vehicle_registration_document_image = isset($vehicle_registration_document) ? asset($vehicle_registration_document->file_name) : null;
        $user->nv_business_image = isset($nv_business_image) ? asset($nv_business_image->file_name) : null;
        // $user->nv_business_image = isset($nv_business_image) ? asset($nv_business_image->file_name) : null;
        $user->vehicle_registration_document_2_image = isset($vehicle_registration_document_2) ? asset($vehicle_registration_document_2->file_name) : null;
        $user->insurance_inspection_image = isset($insurance_inspection) ? asset($insurance_inspection->file_name) : null;;
        $user->userIdentity =  getSingleMedia($user, 'userIdentity', null);
        $user->profile_image = getImagePath($user);
        $response = [
            'message' => "Registered Successfully",
            'data' => $user,
            'status' => true
        ];
        return json_custom_response($response);
    }
    public function register(Request $request)
    {
        // dd($request->userIdentity);
        $input = $request->all();
        $cards = encrypt(json_decode($request->cards));
        $parts = explode(" ", $request->name);
        if (count($parts) > 1) {
            $lastname = array_pop($parts);
            $firstname = implode(" ", $parts);
        } else {
            $firstname = $request->name;
            $lastname = " ";
        }
        $input['first_name'] = $firstname;
        $input['last_name'] = $lastname;
        $input['full_name'] = $request->full_name;
        $input['cards'] = $request->cards;
        $input['contact_number'] = $request->contact_number;
        $password = $input['password'];
        $input['user_type'] = isset($input['user_type']) ? $input['user_type'] : 'rider';
        $input['password'] = Hash::make($password);
        $input['userIdentity_id'] = $request->userIdentity;
        if (in_array($input['user_type'], ['driver'])) {
            $input['status'] = isset($input['status']) ? $input['status'] : 'pending';
        }
        $input['display_name'] = $input['first_name'] . " " . $input['last_name'];
        // $img = explode(",", $request->userIdentity)[1];
        // $base64data = str_replace(',', '', $img);
        $v = DB::transaction(function () use ($request, $input) {
            $user = User::create($input);
            //  Save Rider Detail
            // if (isset($request->userIdentity) && $request->userIdentity != null) {
            //     $user->clearMediaCollection('userIdentity');
            //     $user->addMediaFromBase64($base64data)
            //         ->usingFileName('userIdentity.jpg')
            //         ->toMediaCollection('userIdentity');
            // }
            // $user->getMedia('document_image');
            $riderDetail =  RiderDetail::create([
                'rider_id' => $user->id,
                'card_id' => 0,
                'driver_preference' => $request->driver_prefrences,
                'music_choice' => $request->music,
                // 'image' => getSingleMedia($user, 'userIdentity', null),
            ]);

            AboutMeDetail::create([
                'user_id' => $user->id,
                'fun_fact' => $request->fun_fact ?? null,
                'next_place' => $request->next_place ?? null,
                'movie'  => $request->movie ?? null,
                'fear' => $request->fear ?? null,
                'live_without' => $request->live_without ?? null,
                'obessed_with' => $request->obsessed_with ?? null,
                'next_time' => $request->next_time ?? null,
                // 'image' => getSingleMedia($user, 'userIdentity', null),
            ]);
            $cards = json_decode($request->cards);
            // $cards = json_decode(json_decode($request->cards));
            foreach ($cards as $key => $value) {
                $card = array_merge((array)$value, ['rider_id' => $user->id, 'card_number' => $value->card_number]);
                $savedCard = Card::create($card);
                if ($savedCard->default == 'true')
                    $riderDetail->update(['card_id' => $savedCard->id]);
            }
            $user->assignRole($input['user_type']);
            $user->api_token = $user->createToken('auth_token')->plainTextToken;
            // $user->profile_image = getSingleMedia($user, 'profile_image', null);
            $user->profile_image = getImagePath($user);
            return $user;
        });

        $message = __('message.save_form', ['form' => __('message.' . $input['user_type'])]);
        $response = [
            'status' => true,
            'message' => $message,
            'data' => User::where('id', $v->id)->with(['riderDetail', 'aboutMeDetail'])->first()
        ];
        return json_custom_response($response);
        // });
    }
    
    public function driverRegister(Request $request)
    {
        $input = $request->all();
        $input['first_name'] = $request->first_name;
        $input['last_name'] = $request->last_name;
        $input['email'] = $request->email;
        $input['contact_number'] = $request->contact_number;
        $password =  isset($request->password) ? $request->password : 12345678;
        $input['user_type'] = isset($request->user_type) ? $request->user_type : 'driver';
        $input['password'] = Hash::make($password);
        // $input['status'] = isset($input['status']) ? $input['status']: 'pending';
        $input['status'] = "active";
        $input['display_name'] = $request->first_name . " " . $request->last_name;
        // $input['display_name'] = $request->display_name ?? null;
        $input['is_available'] = 1;
        $input['driver_licence'] = $request->input('driver_licence', null);
        // $input['driver_licence'] = $request->driver_licence;
        $input['business_licence'] = $request->input('business_licence', null);
        $input['vehicle_registration_document'] = $request->input('vehicle_registration_document', null);
        $input['vehicle_registration_document_2'] = $request->input('vehicle_registration_document_2', null);
        $input['insurance_inspection'] = $request->input('insurance_inspection', null);
        // $input['business_licence'] = $request->business_licence;
        // $input['vehicle_registration_document'] = $request->vehicle_registration_document;
        // $input['vehicle_registration_document_2'] = $request->vehicle_registration_document_2;
        // $input['insurance_inspection'] = $request->insurance_inspection;

        // Set is_driver_licence based on the presence of driver_licence value
        $input['is_driver_licence'] = !is_null($input['driver_licence']);
        $input['is_business_licence'] = !is_null($input['is_business_licence']);
        $input['is_vehicle_registration_document'] = !is_null($input['is_vehicle_registration_document']);
        $input['is_vehicle_registration_document_2'] = !is_null($input['is_vehicle_registration_document_2']);
        $input['is_insurance_inspection'] = !is_null($input['is_insurance_inspection']);
        $user = User::create($input);
        $user->assignRole($input['user_type']);

        $user->userDetail()->create([
            'city'  =>  $request->city,
            'state'  =>  $request->state,
            'zip'  =>  $request->zip,
            'home_address' =>  $request->address,
            'car_category_id' => $request->car_category_id,
            'car_model' => $request->car_model,
            'car_plate_number' => $request->car_plate_number,
            'car_made_in' => $request->car_made_in,
        ]);
        $user->aboutMeDetail()->create([
            'user_id' => $request->id,
            'fun_fact' => $request->fun_fact ?? null,
            'next_place' => $request->next_place ?? null,
            'movie'  => $request->movie ?? null,
            'fear' => $request->fear ?? null,
            'live_without' => $request->live_without ?? null,
            'obessed_with' => $request->obsessed_with ?? null,
            'next_time' => $request->next_time ?? null,
            // 'image' => getSingleMedia($user, 'userIdentity', null),
        ]);
        $user =User::where('id',$user->id)->first();
        // $cards = json_decode($request->cards);
        $cards = json_decode(json_decode($request->cards));
        foreach ($cards as $key => $value) {
            $card = array_merge((array)$value, ['rider_id' => $user->id, 'card_number' => $value->card_number]);
            $savedCard = Card::create($card);
            if ($savedCard->default ==  1)
                $user->userDetail->update(['card_id' => $savedCard->id]);
        }
        $user->api_token = $user->createToken('auth_token')->plainTextToken;
        $driver_licence =  Upload::where('id', $user->driver_licence)->first();
        $business_licence =  Upload::where('id', $user->business_licence)->first();
        $vehicle_registration_document =  Upload::where('id', $user->vehicle_registration_document)->first();
        $vehicle_registration_document_2 =  Upload::where('id', $user->vehicle_registration_document_2)->first();
        $insurance_inspection =  Upload::where('id', $user->insurance_inspection)->first();
        $user->driver_licence_image = isset($driver_licence) ? asset($driver_licence->file_name) : null;
        $user->business_licence_image = isset($business_licence) ? asset($business_licence->file_name) : null;
        $user->vehicle_registration_document_image = isset($vehicle_registration_document) ? asset($vehicle_registration_document->file_name) : null;
        $user->vehicle_registration_document_2_image = isset($vehicle_registration_document_2) ? asset($vehicle_registration_document_2->file_name) : null;
        $user->insurance_inspection_image = isset($insurance_inspection) ? asset($insurance_inspection->file_name) : null;;
        $user->userIdentity =  getSingleMedia($user, 'userIdentity', null);
        $response = [
            'message' => "Registered Successfully",
            'data' => $user,
            'status' => true
        ];
        return json_custom_response($response);
    }
    // public function driverRegister(Request $request)
    // {
    //     // $cards = json_decode($request->cards);
    //     $input = $request->all();
    //     $input['first_name'] = $request->first_name;
    //     $input['last_name'] = $request->last_name;
    //     $input['email'] = $request->email;
    //     $input['contact_number'] = $request->contact_number;
    //     $password =  isset($request->password) ? $request->password : 12345678;
    //     $input['user_type'] = isset($request->user_type) ? $request->user_type : 'driver';
    //     $input['password'] = Hash::make($password);
    //     // $input['status'] = isset($input['status']) ? $input['status']: 'pending';
    //     $input['status'] = "active";
    //     $input['display_name'] = $request->first_name . " " . $request->last_name;
    //     // $input['display_name'] = $request->display_name ?? null;
    //     $input['is_available'] = 1;
    //     $input['driver_licence'] = $request->driver_licence;
    //     $input['business_licence'] = $request->business_licence;
    //     $input['vehicle_registration_document'] = $request->vehicle_registration_document;
    //     $input['nv_business_image'] = $request->nv_business_image;
    //     $input['vehicle_registration_document_2'] = $request->vehicle_registration_document_2;
    //     $input['insurance_inspection'] = $request->insurance_inspection;
    //     $user = User::create($input);
    //     $user->assignRole($input['user_type']);

    //     $user->userDetail()->create([
    //         'city'  =>  $request->city,
    //         'state'  =>  $request->state,
    //         'zip'  =>  $request->zip,
    //         'home_address' =>  $request->address,
    //         'car_category_id' => $request->car_category_id,
    //         'car_model' => $request->car_model,
    //         'car_plate_number' => $request->car_plate_number,
    //         'car_made_in' => $request->car_made_in,
    //     ]);
    //     $user->aboutMeDetail()->create([
    //         'user_id' => $request->id,
    //         'fun_fact' => $request->fun_fact ?? null,
    //         'next_place' => $request->next_place ?? null,
    //         'movie'  => $request->movie ?? null,
    //         'fear' => $request->fear ?? null,
    //         'live_without' => $request->live_without ?? null,
    //         'obessed_with' => $request->obsessed_with ?? null,
    //         'next_time' => $request->next_time ?? null,
    //         // 'image' => getSingleMedia($user, 'userIdentity', null),
    //     ]);
    //     $user =User::where('id',$user->id)->first();
    //     $cards = json_decode($request->cards);
    //     // $cards = json_decode(json_decode($request->cards));
    //     foreach ($cards as $key => $value) {
    //         $card = array_merge((array)$value, ['rider_id' => $user->id, 'card_number' => $value->card_number]);
    //         $savedCard = Card::create($card);
    //         if ($savedCard->default ==  1)
    //             $user->userDetail->update(['card_id' => $savedCard->id]);
    //     }
    //     $user->api_token = $user->createToken('auth_token')->plainTextToken;
    //     $driver_licence =  Upload::where('id', $user->driver_licence)->first();
    //     $business_licence =  Upload::where('id', $user->business_licence)->first();
    //     $vehicle_registration_document =  Upload::where('id', $user->vehicle_registration_document)->first();
    //     $nv_business_image =  Upload::where('id', $user->nv_business_image)->first();
    //     $vehicle_registration_document_2 =  Upload::where('id', $user->vehicle_registration_document_2)->first();
    //     $insurance_inspection =  Upload::where('id', $user->insurance_inspection)->first();
    //     $user->driver_licence_image = isset($driver_licence) ? asset($driver_licence->file_name) : null;
    //     $user->business_licence_image = isset($business_licence) ? asset($business_licence->file_name) : null;
    //     $user->vehicle_registration_document_image = isset($vehicle_registration_document) ? asset($vehicle_registration_document->file_name) : null;
    //     $user->nv_business_image = isset($nv_business_image) ? asset($nv_business_image->file_name) : null;
    //     $user->vehicle_registration_document_2_image = isset($vehicle_registration_document_2) ? asset($vehicle_registration_document_2->file_name) : null;
    //     $user->insurance_inspection_image = isset($insurance_inspection) ? asset($insurance_inspection->file_name) : null;;
    //     $user->userIdentity =  getSingleMedia($user, 'userIdentity', null);
    //     $response = [
    //         'message' => "Registered Successfully",
    //         'data' => $user,
    //         'status' => true
    //     ];
    //     return json_custom_response($response);
    // }
    public function forgetPasswordOtp(ForgetPasswordRequest $request)
    {
        $userOtp   = UserOtp::where('otp', $request->otp)->first();
        $now = now();
        if (!$userOtp) {
            return response()->json([
                'status' => false,
                'message' => 'Your OTP is not correct',
            ]);
        } else if ($userOtp && $now->isAfter($userOtp->expire_at)) {
            return response()->json([
                'status' => false,
                'message' => 'Your OTP has been expired',
            ]);
        }
        $user = User::whereId($userOtp->user_id)->first();
        if ($user) {
            $userOtp->update([
                'expire_at' => now()
            ]);
            $user->update([
                'password' => Hash::make($request->password)
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Password reset Successfully',
                'data' => User::all()
            ]);
        }

        // return redirect()->route('otp.login')->with('error', 'Your Otp is not correct');
    }
    // public function driverLocation(Request $request)
    // {
    //     User::where('id', auth()->user()->id)->update([
    //         "latitude" => $request->latitude,
    //         "longitude" => $request->longitude,
    //     ]);
    //     return response()->json([
    //         'data' => User::where('id', auth()->user()->id)->first(),
    //         'status' => true,
    //         'message' => 'Location Updated Successfully',
    //     ]);
    // }
    
    public function driverLocation(Request $request)
{
    User::where('id', auth()->user()->id)->update([
        "latitude" => $request->latitude,
        "longitude" => $request->longitude,
    ]);

    if ($request->is('api*')) {
        // Update is_online status to 1 (online) for API calls
        User::where('id', auth()->user()->id)->update(['is_online' => 1]);
    }

    return response()->json([
        'data' => User::where('id', auth()->user()->id)->first(),
        'status' => true,
        'message' => 'Location Updated Successfully',
    ]);
}

    public function getforgetPasswordOtp(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        /* User Does not Have Any Existing OTP */
        if (isset($user)) {
            $userOtp = UserOtp::where('user_id', $user->id)->first();
            $now = now();
            // if($userOtp && $now->isBefore($userOtp->expire_at)){
            //     return $userOtp;
            // }
            /* Create a New OTP */
            if ($userOtp) {
                UserOtp::where('user_id', $user->id)->update([
                    'user_id' => $user->id,
                    'otp' => rand(123456, 999999),
                    'expire_at' => $now->addMinutes(10)
                ]);
                $userOtp = UserOtp::where('user_id', $user->id)->first();
                $userOtp->sendSMS($user->contact_number);
                return response()->json([
                    'status' => true,
                    'message' => 'Your 6 digit Otp has been resent.',
                ]);
            } else {
                $userOtp = UserOtp::create([
                    'user_id' => $user->id,
                    'otp' => rand(123456, 999999),
                    'expire_at' => $now->addMinutes(10)
                ]);
                $userOtp->sendSMS($user->contact_number);
                return response()->json([
                    'status' => true,
                    'message' => 'Your 6 digit Otp has been sent.',
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Sorry, Couldn\'t find any user with given email Id..',
            ]);
        }
    }

    public function driverPreferences()
    {
        return response()->json([
            'data' => DriverPreference::all(),
            'status' => true,
            'message' => 'Driver Preferences retrieved Successfully',
        ]);
    }

    public function musics()
    {
        return response()->json([
            'data' => \DB::table('musics')->get(),
            'status' => true,
            'message' => 'musics retrieved Successfully',
        ]);
    }

    public function emailCheck(Request $request)
    {
        $input['email'] = $request->email;
        $input['username'] = $request->username;
        // Must not already exist in the `email` column of `users` table
        $rules = array('email' => 'unique:users,email', 'username' => 'unique:users,username');

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->getMessageBag()->get('*')
            ]);
        } else {
            return response()->json([
                'status' => true,
                'message' => 'Valid Email and Username.',
            ]);
        }
    }
    public function generateOtp(Request $request)
    {
        // $request->validate([
        //     'phone' => 'required|exists:users,contact_number'
        // ]);
        $user = User::where('contact_number', $request->phone)->first();
        // if(!isset($user))
        // {
        //     return response()->json([
        //         'status'=>false, 
        //         'message'=>'User with this contact number not found.', 
        //   ]);
        // }
        $now = now();
        // if(isset($user))
        // {
        //      /* User Does not Have Any Existing OTP */
        //     $userOtp = UserOtp::where('user_id', $user->id)->first();

        //     // if($userOtp && $now->isBefore($userOtp->expire_at)){
        //     //     return $userOtp;
        //     // }
        //     /* Create a New OTP */
        //     if($userOtp)
        //     {
        //         UserOtp::where('user_id', $user->id)->update([
        //             'user_id' => $user->id,
        //             'otp' => mt_rand(100000,999999),
        //             'expire_at' => $now->addMinutes(10)
        //         ]);
        //         $userOtp=UserOtp::where('user_id', $user->id)->first();
        //         $userOtp->sendSMS($user->contact_number);
        //         return response()->json([
        //                 'status'=>true, 
        //                 'message'=>'Your 6 digit Otp has been resent.', 
        //         ]);
        //     }
        //     else
        //     {
        //         $userOtp=UserOtp::create([
        //         'user_id' => $user->id,
        //         'otp' => mt_rand(100000,999999),
        //         'expire_at' => $now->addMinutes(10)
        //        ]);
        //         $userOtp->sendSMS($user->contact_number);
        //             return response()->json([
        //                     'status'=>true, 
        //                     'message'=>'Your 6 digit Otp has been sent.', 
        //                 ]);
        //     }
        // }
        // else
        // {
        $userOtp = UserOtp::create([
            'user_id' => 0,
            'otp' => mt_rand(100000, 999999),
            'expire_at' => $now->addMinutes(10)
        ]);

        $status = $userOtp->sendSMS($request->phone);
        if ($status) {
            return response()->json([
                'status' => true,
                'message' => 'Your 6 digit Otp has been sent.',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Otp not  sent.',
            ]);
        }

        // }
    }
    public function loginWithOtp(Request $request)
    {
        /* Validation */
        // $request->validate([
        //     'user_id' => 'required|exists:users,id',
        //     'otp' => 'required'
        // ]);  
        /* Validation Logic */
        // $userOtp   = UserOtp::where('otp', $request->otp)->first();
        // $now = now();
        // if (!$userOtp) {
        //     return response()->json([
        //         'status'=>true, 
        //         'message'=>'Your OTP is not correct', 
        //     ]);
        // }else if($userOtp && $now->isAfter($userOtp->expire_at)){
        //     return response()->json([
        //         'status'=>true, 
        //         'message'=>'Your OTP has been expired', 
        //     ]);
        // }

        $userOtp = UserOtp::whereOtp($request->otp)->first();
        if ($userOtp) {
            $userOtp->update([
                'expire_at' => now()
            ]);
            // Auth::login($user);
            return response()->json([
                'status' => true,
                'message' => 'OTP Matched',
                // 'data'=>User::all()
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Your OTP is not correct',
            ]);
        }

        // return redirect()->route('otp.login')->with('error', 'Your Otp is not correct');
    }
    public function twillio2Old(Request $request)
    {
        $twilio_number = "+14694253422";
        $account_sid = getenv("TWILIO_SID");
        // $auth_token = getenv("TWILIO_AUTH_TOKEN");
        // $twilio_verify_sid = getenv("TWILIO_VERIFY_SID");
        $auth_token = "025cd6ec820c2efb5012b99a2e5f050a";
        $account_sid = "AC0e9de838f1a9c0fdea308d4f2a16f92a";
        $twilio_verify_sid = "VAac213514ead4766e3a1df095ac451a8b";
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL,"https://verify.twilio.com/v2/Services/VA286b596a315e55a21afa6416e8efd9bd/Verifications \
        // --data-urlencode 'To=+14694253422' \
        // --data-urlencode 'Channel=sms' \
        // -u $account_sid:$auth_token");
        // $server_output = curl_exec($ch);
        // $error    = curl_errno($ch);
        // dd( $error);
        // curl_close ($ch);

        // $twilio_verify_sid = "MG9a4512cff0eae08b822bd34f5d7843c9";
        $client = new Client($account_sid, $auth_token);
        // dd($client); 
        $client->verify->v2->services($twilio_verify_sid)
            ->verifications
            ->create($twilio_number, "sms");


        // $client->verify->v2->services($twilio_verify_sid)
        //     ->verifications
        //     ->create($twilio_number, "sms");

        // $client->messages->create(
        //     // Where to send a text message (your cell phone?)
        //     '+92 316 5328440',
        //     array(
        //         'from' => $twilio_number,
        //         'body' => 'I sent this message in under 10 minutes!'
        //     )
        // );
        dd("fkfk");
    }
    public function verifyotp(Request $request)
    {
        // $data = $request->validate([
        //     'verification_code' => ['required', 'numeric'],
        //     'phone_number' => ['required', 'string'],
        // ]);
        // dd("dd");
        /* Get credentials from .env */
        // $token = getenv("TWILIO_AUTH_TOKEN");
        // $twilio_sid = getenv("TWILIO_SID");
        // $twilio_verify_sid = getenv("TWILIO_VERIFY_SID");
        $token = "b1fbca5f7c7ba3c02fdeb324b4f81c2c";
        $twilio_sid = "AC87dab0199e8fdeedc0aa66c18a1e3a02";
        $twilio_verify_sid = "VA61fac4e4f8b7c95c2ff54bb689102332";
        $twilio = new Client($twilio_sid, $token);
        $verification_check = $twilio->verify->v2->services("VA61fac4e4f8b7c95c2ff54bb689102332")
            ->verificationChecks
            ->create(
                [
                    "to" => "+18623045264",
                    "code" => "3026449"
                ]
            );
        return  $verification_check;
        //      print($verification_check->status);
        // $verification = $twilio->verify->v2->services($twilio_verify_sid)
        //     ->verificationChecks
        //     ->create(['code' => $data['verification_code'], 'to' => "+14694253422"]);
        //     dd($verification);
        // if ($verification->valid) {
        //     $user = tap(User::where('contact_number', $data['phone_number']))->update(['is_verified_driver' => 1]);
        //     /* Authenticate user */
        //     Auth::login($user->first());
        //     return redirect()->route('home')->with(['message' => 'Phone number verified']);
        // }
        return back()->with(['phone_number' => $data['phone_number'], 'error' => 'Invalid verification code entered!']);
    }

    public function createImage(Request $request)
    {
        $img = $request->image;
        $folderPath = 'public/uploads';
        $image_parts = explode(";base64,", $img);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $path = $folderPath . '/' . uniqid() . '.' . $image_type;
        file_put_contents($path, $image_base64);
        $image =  Upload::create([
            'file_original_name' => null, 'file_name' =>  $path,
            'user_id' => $request->id, 'extension' => $image_type,
            'type' => "others", 'file_size' => 0
        ]);
        if (isset($request->id)) {
            RideRequest::where('id', $request->id)->update(['ride_image_id' => $image->id]);
        }

        $response = [
            'status' => true,
            'message' => "Image Created Successfully",
            'data' => $image
        ];
        return json_custom_response($response);
    }



// public function login(Request $request)
// {
//     $credentials = [
//         'username' => $request->input('username'),
//         'user_type' => $request->input('type')
//     ];

//     $user = User::where('username', $credentials['username'])
//                 ->where('user_type', $credentials['user_type'])
//                 ->first();

//     if ($user && Hash::check($request->input('password'), $user->password)) {
//         $success = User::where('id', $user->id)
//             ->where('user_type', $credentials['user_type'])
//             ->first();

//         $success['api_token'] = $user->createToken('auth_token')->plainTextToken;
//         $success['profile_image'] = getSingleMedia($user, 'profile_image', null);

//         $is_verified_driver = false;
//         if ($user->user_type === 'driver') {
//             $is_verified_driver = $user->is_verified_driver;
//         }
//         $success['is_verified_driver'] = (int) $is_verified_driver;

//         return json_custom_response([
//             'data' => $success,
//             'status' => true,
//             'message' => "Login Successfully"
//         ], 200);
//     }

//     return response()->json([
//         'status' => false,
//         'message' => "These credentials do not match our records."
//     ], 400);
// }

    public function login(Request $request)
    {
        
        if (Auth::attempt(['username' => request('username'), 'email' => function ($query) use ($request) {
            $query->orwhere('email', request('username'));
        }, 'user_type' => function ($query) use ($request) {
            $query->where('user_type', $request->type);
        }, 'password' => request('password')])) {
            $user = Auth::user();
            $success = User::where('id', $user->id)->where('user_type', $request->type)->first();
            $success['api_token'] = $user->createToken('auth_token')->plainTextToken;
           $success['profileImage'] = getImagePath($user);
             $success['profile_image'] = getSingleMedia($user,'profile_image',null);
            $is_verified_driver = false;
            if ($user->user_type == 'driver') {
                $is_verified_driver = $user->is_verified_driver; // DriverDocument::verifyDriverDocument($user->id);
            }
            $success['is_verified_driver'] = (int) $is_verified_driver;
            unset($success['media']);
            return json_custom_response(['data' => $success, 'status' => true, 'message' => "Login Successfully"], 200);
        } else {
            // $message = __('auth.failed');
            $response = [
                'status' => false,
                'message' => "These credentials do not match our records."
            ];
            return response()->json($response, 400);
        }
    }

    public function userList(Request $request)
    {
        $user_type = isset($request['user_type']) ? $request['user_type'] : 'rider';

        $user_list = User::query();

        $user_list->when(request('user_type'), function ($q) use ($user_type) {
            return $q->where('user_type', $user_type);
        });

        $user_list->when(request('fleet_id'), function ($q) {
            return $q->where('fleet_id', request('fleet_id'));
        });

        if ($request->has('is_online') && isset($request->is_online)) {
            $user_list = $user_list->where('is_online', request('is_online'));
        }

        if ($request->has('status') && isset($request->status)) {
            $user_list = $user_list->where('status', request('status'));
        }

        $per_page = config('constant.PER_PAGE_LIMIT');
        if ($request->has('per_page') && !empty($request->per_page)) {
            if (is_numeric($request->per_page)) {
                $per_page = $request->per_page;
            }
            if ($request->per_page == -1) {
                $per_page = $user_list->count();
            }
        }

        $user_list = $user_list->paginate($per_page);

        if ($user_type == 'driver') {
            $items = DriverResource::collection($user_list);
        } else {
            $items = UserResource::collection($user_list);
        }

        $response = [
            'status' => true,
            'pagination' => json_pagination_response($items),
            'data' => $items,
        ];

        return json_custom_response($response);
    }

    public function userDetail(Request $request)
    {
        $id = $request->id;
        $user = User::where('id', $id)->first();
        if (empty($user)) {
            $message = __('message.user_not_found');
            return json_message_response($message, 400);
        }
        if ($user->user_type == 'driver') {
            $user_detail = new DriverResource($user);
        } else {
            $user_detail = new UserResource($user);
        }

        $response = [
            'status' => true,
            'data' => $user_detail
        ];
        return json_custom_response($response);
    }

    public function changePassword(Request $request)
    {
        $user = User::where('id', Auth::user()->id)->first();
        if ($user == "") {
            $message = __('message.user_not_found');
            return json_message_response($message, 400);
        }

        $hashedPassword = $user->password;

        $match = Hash::check($request->old_password, $hashedPassword);

        $same_exits = Hash::check($request->new_password, $hashedPassword);
        if ($match) {
            if ($same_exits) {
                $message = __('message.old_new_pass_same');
                return json_message_response($message, 400);
            }

            $user->fill([
                'password' => Hash::make($request->new_password)
            ])->save();

            $message = __('message.password_change');
            return json_message_response($message, 200);
        } else {
            $message = __('message.valid_password');
            return json_message_response($message, 400);
        }
    }

    public function updateProfile(UserRequest $request)
    {
        $user = Auth::user();
        if ($request->has('id') && !empty($request->id)) {
            $user = User::where('id', $request->id)->first();
        }
        if ($user == null) {
            return json_message_response(__('message.no_record_found'), 400);
        }

        $user->fill($request->all())->update();

        if (isset($request->profile_image) && $request->profile_image != null) {
            $user->clearMediaCollection('profile_image');
            $user->addMediaFromRequest('profile_image')->toMediaCollection('profile_image');
        }

        $user_data = User::find($user->id);

        if ($user_data->userDetail != null && $request->has('user_detail')) {
            $user_data->userDetail->fill($request->user_detail)->update();
        } else if ($request->has('user_detail') && $request->user_detail != null) {
            $user_data->userDetail()->create($request->user_detail);
        }

        if ($user_data->userBankAccount != null && $request->has('user_bank_account')) {
            $user_data->userBankAccount->fill($request->user_bank_account)->update();
        } else if ($request->has('user_bank_account') && $request->user_bank_account != null) {
            $user_data->userBankAccount()->create($request->user_bank_account);
        }

        $message = __('message.updated');
        // $user_data['profile_image'] = getSingleMedia($user_data,'profile_image',null);
        unset($user_data['media']);

        if ($user_data->user_type == 'driver') {
            $user_resource = new DriverResource($user_data);
        } else {
            $user_resource = new UserResource($user_data);
        }

        $response = [
            'status' => true,
            'data' => $user_resource,
            'message' => $message
        ];

        return json_custom_response($response);
    }

    // public function logout(Request $request)
    // {
    //     if ($request->is('api*')) {
    //         $request->user()->currentAccessToken()->delete();
    //         $response = [
    //             'status' => true,
    //             'data' => null,
    //             'message' => "Successfully Logging out"
    //         ];
    //         return json_custom_response($response);
    //     }
    // }
    
    public function logout(Request $request)
{
    if ($request->is('api*')) {
        $user = $request->user();

        // Check if user is logged in
        if ($user) {
            // Update is_online status to 0 (offline)
            User::where('id', $user->id)->update(['is_online' => 0]);
        }

        $user->currentAccessToken()->delete();

        $response = [
            'status' => true,
            'data' => null,
            'message' => "Successfully Logging out"
        ];
        return json_custom_response($response);
    }
}


    public function forgetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $response = Password::sendResetLink(
            $request->only('email')
        );

        return $response == Password::RESET_LINK_SENT
            ? response()->json(['message' => __($response), 'status' => true], 200)
            : response()->json(['message' => __($response), 'status' => false], 400);
    }

    public function socialLogin(Request $request)
    {
        $input = $request->all();

        if ($input['login_type'] === 'mobile') {
            $user_data = User::where('username', $input['username'])->where('login_type', 'mobile')->first();
        } else {
            $user_data = User::where('email', $input['email'])->first();
        }

        if (!in_array($user_data->user_type, ['admin', request('user_type')])) {
            $message = __('auth.failed');
            return json_message_response($message, 400);
        }

        if ($user_data != null) {
            if (!isset($user_data->login_type) || $user_data->login_type  == '') {
                if ($request->login_type === 'google') {
                    $message = __('validation.unique', ['attribute' => 'email']);
                } else {
                    $message = __('validation.unique', ['attribute' => 'username']);
                }
                return json_message_response($message, 400);
            }
            $message = __('message.login_success');
        } else {

            if ($request->login_type === 'google') {
                $key = 'email';
                $value = $request->email;
            } else {
                $key = 'username';
                $value = $request->username;
            }

            if ($request->login_type === 'mobile' && $user_data == null) {
                $otp_response = [
                    'status' => true,
                    'is_user_exist' => false
                ];
                return json_custom_response($otp_response);
            }

            $password = !empty($input['accessToken']) ? $input['accessToken'] : $input['email'];

            $input['display_name'] = $input['first_name'] . " " . $input['last_name'];
            $input['password'] = Hash::make($password);
            $input['user_type'] = isset($input['user_type']) ? $input['user_type'] : 'rider';
            $user = User::create($input);
            if ($user->userWallet == null) {
                $user->userWallet()->create(['total_amount' => 0]);
            }
            $user->assignRole($input['user_type']);

            $user_data = User::where('id', $user->id)->first();
            $message = __('message.save_form', ['form' => $input['user_type']]);
        }

        $user_data['api_token'] = $user_data->createToken('auth_token')->plainTextToken;
        $user_data['profile_image'] = getSingleMedia($user_data, 'profile_image', null);

        $is_verified_driver = false;
        if ($user_data->user_type == 'driver') {
            $is_verified_driver = $user_data->is_verified_driver; // DriverDocument::verifyDriverDocument($user_data->id);
        }
        $user_data['is_verified_driver'] = (int) $is_verified_driver;
        $response = [
            'status' => true,
            'message' => $message,
            'data' => $user_data
        ];
        return json_custom_response($response);
    }

    public function updateUserStatus(Request $request)
    {
        $user_id = $request->id ?? auth()->user()->id;

        $user = User::where('id', $user_id)->first();

        if ($user == "") {
            $message = __('message.user_not_found');
            return json_message_response($message, 400);
        }
        if ($request->has('status')) {
            $user->status = $request->status;
        }
        if ($request->has('is_online')) {
            $user->is_online = $request->is_online;
        }
        if ($request->has('is_available')) {
            $user->is_available = $request->is_available;
        }
        if ($request->has('latitude')) {
            $user->latitude = $request->latitude;
        }
        if ($request->has('longitude')) {
            $user->longitude = $request->longitude;
        }
        $user->save();

        if ($user->user_type == 'driver') {
            $user_resource = new DriverResource($user);
        } else {
            $user_resource = new UserResource($user);
        }
        $message = __('message.update_form', ['form' => __('message.status')]);
        $response = [
            'status' => true,
            'data' => $user_resource,
            'message' => $message
        ];
        return json_custom_response($response);
    }

    public function updateAppSetting(Request $request)
    {
        $data = $request->all();
        AppSetting::updateOrCreate(['id' => $request->id], $data);
        $message = __('message.save_form', ['form' => __('message.app_setting')]);
        $response = [
            'data' => AppSetting::first(),
            'message' => $message
        ];
        return json_custom_response($response);
    }

    public function getAppSetting(Request $request)
    {
        if ($request->has('id') && isset($request->id)) {
            $data = AppSetting::where('id', $request->id)->first();
        } else {
            $data = AppSetting::first();
        }

        return json_custom_response($data);
    }

    public function deleteUserAccount(Request $request)
    {
        $id = auth()->id();
        $user = User::where('id', $id)->first();
        $message = __('message.not_found_entry', ['name' => __('message.account')]);

        if ($user != '') {
            $user->delete();
            $message = __('message.account_deleted');
        }

        return json_custom_response(['message' => $message, 'status' => true]);
    }
}
