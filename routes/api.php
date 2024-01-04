
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers;
use App\Http\Controllers\API;
use App\Http\Controllers\ChatsController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\MoneySetupController;
use App\Http\Controllers\RiderController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\vendor\Chatify\Api\MessagesController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
////////////// OTP ////////////////

// Route::get('/test/purchase', 'OtpController@confirmationPage');
// Route::post('/test/otp-request', 'OtpController@requestForOtp')->name('requestForOtp');
// Route::post('/test/otp-validate', 'OtpController@validateOtp')->name('validateOtp');
// Route::post('/test/otp-resend', 'OtpController@resendOtp')->name('resendOtp');

/////////////// End OTP //////////////
// Route::get('/chat', [ChatsController::class,'index']);


Route::get('fetchMessages/{id}', [ChatsController::class, 'fetchUserMessages']);
// Route::post('sendMessage', [ChatsController::class,'send'])->name('send.message');
Route::get('pushNotification', [API\UserController::class, 'pushNotification']);
Route::get('inboxes', [ChatsController::class, 'fetchAllInboxes']);
Route::get('faqs', [FaqController::class, 'index']);
Route::post('support', [FaqController::class, 'support']);
Route::post('subscription-renewal', [FaqController::class, 'subscriptionRenewal']);
Route::post('sendMessages', [ChatsController::class, 'sendMessage']);
Route::post('otp', [API\UserController::class, 'generateOtp']);
Route::post('verifyotp', [API\UserController::class, 'loginWithOtp']);
Route::post('forgetPasswordOtp', [API\UserController::class, 'forgetPasswordOtp']);
Route::post('getforgetPasswordOtp', [API\UserController::class, 'getforgetPasswordOtp']);
Route::get('driverPreferences', [API\UserController::class, 'driverPreferences']);
Route::get('musics', [API\UserController::class, 'musics']);
Route::post('register', [API\UserController::class, 'register']);
Route::post('driver-register', [API\UserController::class, 'driverRegister']);
Route::post('login', [API\UserController::class, 'login']);
Route::post('saveImage', [API\UserController::class, 'createImage']);
Route::post('emailCheck', [API\UserController::class, 'emailCheck']);
Route::post('forget-password', [API\UserController::class, 'forgetPassword']);
Route::post('social-login', [API\UserController::class, 'socialLogin']);
Route::resource('region', RegionController::class);
Route::get('user-list', [API\UserController::class, 'userList']);
Route::post('user-detail', [API\UserController::class, 'userDetail']);
Route::get('document-list', [API\DocumentController::class, 'getList']);
Route::get('service-list', [API\ServiceController::class, 'getList']);
Route::get('getCarCategories', [API\ServiceController::class, 'getCarCategories']);
Route::post('estimate-price-time', [API\ServiceController::class, 'estimatePriceTime']);
Route::get('notification-list', [API\NotificationController::class, 'getList']);
Route::get('setReadNotification', [API\NotificationController::class, 'setReadNotification']);
// Route::get('addmoney/stripe', array('as' => 'addmoney.paystripe','uses' => [MoneySetupController::class,'PaymentStripe']));

Route::group(['middleware' => ['auth:sanctum']], function () {  
    Route::get('getBalance', [API\UserController::class, 'getBalance'])->name('getBalance');
    Route::post('profileImage', [API\UserController::class, 'profileImage'])->name('profileImage');
    Route::post('purchaseToken', [API\UserController::class, 'purchaseToken'])->name('purchaseToken');
    Route::post('subscription', [API\UserController::class, 'subscription'])->name('subscription');
    Route::post('notification-list', [API\NotificationController::class, 'getList']);
    Route::get('getUserDetail', [API\UserController::class, 'getUserDetail'])->name('getUserDetail');
    Route::post('riderUpdate', [API\UserController::class, 'riderUpdate'])->name('riderUpdate');
    Route::post('driverUpdate', [API\UserController::class, 'driverUpdate'])->name('driverUpdate');
    Route::post('pusher/auth', [MessagesController::class, 'pusherAuth'])->name('api.pusher.auth');
    Route::post('nearbyDrivers', [App\Http\Controllers\RideRequestController::class, 'nearbyDrivers'])->name('nearbyDrivers');
    Route::post('activeRide', [App\Http\Controllers\RideRequestController::class, 'activeRide'])->name('activeRide');
    Route::get('riderequest-list', [API\RideRequestController::class, 'getList']);
    Route::post('commingDriverStatus', [RiderController::class, 'commingDriverStatus'])->name('commingDriverStatus');
    Route::post('driverLocation', [API\UserController::class, 'driverLocation']);
    Route::get('getRiderCards', [RiderController::class, 'getRiderCards'])->name('getRiderCards');
    Route::post('makeSeen', [MessagesController::class, 'seen'])->name('api.messages.seen');
    Route::post('deleteConversation', [MessagesController::class, 'deleteConversation'])->name('api.conversation.delete');
    Route::post('deleteMessage', [MessagesController::class, 'deleteMessage'])->name('api.message.delete');
    Route::post('sendMessage', [MessagesController::class, 'send'])->name('api.send.message');
    Route::post('fetchMessages', [MessagesController::class, 'fetch'])->name('api.fetch.messages');
    Route::get('getContacts', [MessagesController::class, 'getContacts'])->name('api.contacts.get');
    Route::get('logout', [API\UserController::class, 'logout']);
    Route::get('isonline', [API\UserController::class, 'isonline']);
    Route::get('driver-document-list', [API\DriverDocumentController::class, 'getList']);
    Route::post('driver-document-save', [App\Http\Controllers\DriverDocumentController::class, 'store']);
    Route::post('driver-document-update/{id}', [App\Http\Controllers\DriverDocumentController::class, 'update']);
    Route::post('driver-document-delete/{id}', [App\Http\Controllers\DriverDocumentController::class, 'destroy']);
    Route::post('verify-coupon', [API\RideRequestController::class, 'verifyCoupon']);
    Route::post('save-riderequest', [App\Http\Controllers\RideRequestController::class, 'store']);
    Route::post('findDriver', [App\Http\Controllers\RideRequestController::class, 'findDriver']);
    Route::post('riderequest-update/{id}', [App\Http\Controllers\RideRequestController::class, 'update']);
    Route::get('riderequest-list', [API\RideRequestController::class, 'getList']);
    Route::post('riderequest-cancel', [API\RideRequestController::class, 'cancelRide']);
    Route::post('riderequest-respond', [API\RideRequestController::class, 'respondRideRequest']);
    Route::post('getHistory', [API\RideRequestController::class, 'getHistory']);
    Route::post('riderequest-arrived', [API\RideRequestController::class, 'arrivedRide']);
    Route::get('riderequest-detail', [API\RideRequestController::class, 'getDetail']);
    Route::post('riderequest-delete/{id}', [App\Http\Controllers\RideRequestController::class, 'destroy']);
    Route::get('coupon-list', [API\CouponController::class, 'getList']);

    Route::post('complete-riderequest', [API\RideRequestController::class, 'completeRideRequest']);
    Route::post('save-wallet', [API\WalletController::class, 'saveWallet']);
    Route::get('wallet-list', [API\WalletController::class, 'getList']);

    Route::get('payment-gateway-list', [API\PaymentGatewayController::class, 'getList']);
    Route::get('sos-list', [API\SosController::class, 'getList']);
    Route::post('save-sos', [App\Http\Controllers\SosController::class, 'store']);
    Route::post('sos-update/{id}', [App\Http\Controllers\SosController::class, 'update']);
    Route::post('sos-delete/{id}', [App\Http\Controllers\SosController::class, 'destroy']);
    Route::post('admin-sos-notify', [API\SosController::class, 'adminSosNotify']);
    Route::post('save-ride-rating', [API\RideRequestController::class, 'rideRating']);
    Route::post('get-rating', [API\RideRequestController::class, 'getRating']);
    Route::post('save-payment', [API\PaymentController::class, 'paymentSave']);
    Route::get('withdrawrequest-list', [API\WithdrawRequestController::class, 'getList']);
    Route::post('save-withdrawrequest', [App\Http\Controllers\WithdrawRequestController::class, 'store']);
    Route::post('update-status/{id}', [App\Http\Controllers\WithdrawRequestController::class, 'updateStatus']);
    Route::post('save-complaint', [App\Http\Controllers\ComplaintController::class, 'store']);
    Route::post('update-complaint/{id}', [App\Http\Controllers\ComplaintController::class, 'update']);
    Route::get('admin-dashboard', [API\DashboardController::class, 'adminDashboard']);
    Route::get('rider-dashboard', [API\DashboardController::class, 'riderDashboard']);
    Route::get('current-riderequest', [API\DashboardController::class, 'currentRideRequest']);
    Route::post('earning-list', [API\PaymentController::class, 'DriverEarningList']);
    Route::post('update-profile', [API\UserController::class, 'updateProfile']);
    Route::post('change-password', [API\UserController::class, 'changePassword']);
    Route::post('update-user-status', [API\UserController::class, 'updateUserStatus']);
    Route::post('delete-user-account', [API\UserController::class, 'deleteUserAccount']);
    Route::get('additional-fees-list', [API\AdditionalFeesController::class, 'getList']);
    Route::get('token-list', [API\TokenController::class, 'index']);
    Route::get('token/{id}', [API\TokenController::class, 'show']);
    Route::get('subscription/{id}', [API\SubscriptionController::class, 'show']);

});
