<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ChatsController extends Controller
{
public function __construct()
{
//   $this->middleware('auth');
}
/**
 * Show chats
 *
 * @return \Illuminate\Http\Response
 */
public function index()
{
  return view('chat');
}

/**
 * Fetch all messages
 *
 * @return Message
 */
    public function fetchMessages($id)
    {
        $data = Message::where('user_id',$id)->get();
            return response()->json([
                'data' => $data,
                'status'=>true, 
                'message'=>'Message Sent Successfully.', 
            ]);
    }
    public function fetchAllInboxes()
    {
        $data = User::with('messages')->get();
        // $data = Message::where('user_id',$id)->get();
        return response()->json([
            'data' => $data,
            'status'=>true, 
            'message'=>'Message Sent Successfully.', 
        ]);
    }

/**
 * Persist message to database
 *
 * @param  Request $request
 * @return Response
 */
    public function sendMessage(Request $request)
    {
        $user = User::where('id',$request->user_id)->first();
        if(isset($user->messages))
        {
              $message = $user->messages()->create([
                        'message' => $request->message
                    ]);
        }
      
        return response()->json([
            'status'=>true, 
            'message'=>'Message Sent Successfully.', 
        ]);
    }
}
