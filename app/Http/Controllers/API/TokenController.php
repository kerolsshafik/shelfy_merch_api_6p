<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Token;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    public function store_token(Request $request)
    {
        $user = auth('api')->user();

        $token_check = Token::where('user_id', $user->id)->where('token', $request->token)->first();
        // dd($token_check);
        if (!is_object($token_check)) {
            //Token::where('user_id',$user->id)->delete();
            Token::create(['user_id' => $user->id, 'token' => $request->token]);
            return response()->json([
                'success' => true,
                'message' => 'New Token  Stored Successfully.',

            ], 201);

        }

        return response()->json([
            'success' => true,
            'message' => 'Token is Already Stored Successfully.',

        ], 201);

    }

}
