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
  
            

            $account_sid = "sid here";
            $auth_token = "toker here";
            $twilio_number = "number here";

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
