<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Twilio\Rest\Client;
use Exception;
class UserOtp extends Model
{
    use HasFactory;
    protected $guarded = [''];
    public function sendSMS($receiverNumber1)
    {
        $message = "Login OTP is ".$this->otp;
        try {
  
            // $account_sid = "AC0e9de838f1a9c0fdea308d4f2a16f92a";
            // $auth_token = "025cd6ec820c2efb5012b99a2e5f050a";
            // $twilio_number = "+14694253422";

            $account_sid = "AC0f6ca82d4dbf0e532151cce04728a545";
            $auth_token = "68c50202ff20f08234fa531474b86d3b";
            $twilio_number = "+18884734545";

            $client = new Client($account_sid, $auth_token);
            $client->messages->create($receiverNumber1, [
                'from' => $twilio_number, 
                'body' => $message
            ]);
            return true;
    
        } catch (Exception $e) {
            return false;
            return info("Error: ". $e->getMessage());
        }
    }
}
