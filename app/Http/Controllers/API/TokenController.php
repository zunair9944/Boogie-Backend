<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Token;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    public function index()
    {
        $tokens = Token::all();
        return response()->json([
            'status' => true,
            'message' => 'Tokens list Retrieved Successfully',
            'token' => $tokens
        ]);
    }

    public function show($id)
    {
        $token = Token::find($id);

        if (!$token) {
            return response()->json(['message' => 'Token not found'], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Token Retrieved Successfully',
            'token' => $token
        ]);
    }
}
