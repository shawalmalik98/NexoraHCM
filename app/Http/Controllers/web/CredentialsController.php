<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CredentialsController extends Controller
{
    // Firebase web config for the chat screens (help desk, interviews, meetings); set FIREBASE_* in .env to enable
    protected function firebase_config(Request $request){
        $header_token = $request->header('authentication-token');
        $firebase_config = config('services.firebase.web');

        if(!empty(session('user_session')) && !empty($header_token) && !empty($firebase_config['apiKey'])){
            return response()->json([
                'status' => 'TRUE',
                'data' => $firebase_config
            ]);
        }

        return response()->json([
            'status' => 'FALSE',
        ]);
    }
}
