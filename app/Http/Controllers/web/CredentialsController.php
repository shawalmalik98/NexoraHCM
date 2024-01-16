<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CredentialsController extends Controller
{
    protected function firebase_config(Request $request){
        $header_token = $request->header('authentication-token');
        if(!empty(session('user_session')) && !empty($header_token)){
            if(!env('APP_DEBUG') && env('APP_URL') === 'http://127.0.0.1:8000/'){
                $firebase_config = [
                    'apiKey' => 'AIzaSyCvouAhrVx828PHS5gleO1yqfGD3v7WyY4',
                    'authDomain' => 'iil-hcm.firebaseapp.com',
                    'databaseURL' => 'https://iil-hcm-default-rtdb.firebaseio.com/',
                    'projectId' => 'iil-hcm',
                    'storageBucket' => 'iil-hcm.appspot.com',
                    'messagingSenderId' => 'iil-hcm.appspot.com',
                    'appId' => '1:788291089347:android:8c9362d54733b29f9cc376',
                    'measurementId' => 'G-GFKQGFN0Z0'
                ];
                return response()->json([
                    'status' => 'TRUE',
                    'data' => $firebase_config
                ]);
            }
        }else{
            return response()->json([
                'status' => 'FALSE',
            ]);
        }
    }

    // protected function firebase_config(Request $request){
    //     $header_token = $request->header('authentication-token');
    //     if(!empty(session('user_session')) && !empty($header_token) && $header_token == 'h(mF!reba$e!<ey'){
    //         if(!env('APP_DEBUG') && env('APP_URL') === 'https://sourcecodeemployees.com/'){
    //             $firebase_config = [
    //                 'apiKey' => 'AIzaSyB29LGaiy18oHJY5BzrlCxK0IO3RYiuqqI',
    //                 'authDomain' => 'sourcecode-apps-d94da.firebaseapp.com',
    //                 'databaseURL' => 'https://sourcecode-apps-d94da-hcm.firebaseio.com/',
    //                 'projectId' => 'sourcecode-apps-d94da',
    //                 'storageBucket' => 'sourcecode-apps-d94da.appspot.com',
    //                 'messagingSenderId' => '12606959618',
    //                 'appId' => '1:12606959618:web:36380f75aa80a6569ff71b',
    //                 'measurementId' => 'G-K4KVZ1YQDE'
    //             ];
    //             return response()->json([
    //                 'status' => 'TRUE',
    //                 'data' => $firebase_config
    //             ]);
    //         }
    //     }else{
    //         return response()->json([
    //             'status' => 'FALSE',
    //         ]);
    //     }
    // }
}
