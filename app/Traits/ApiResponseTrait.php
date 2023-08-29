<?php

namespace App\Traits;

trait ApiResponseTrait {

    public function api_response_json($status,$message,$data,$response_code,...$other_data){

        $response_array = [
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ];

        if(!empty($other_data)){

            foreach($other_data[0] as $key => $other){

                $response_array[$key] = $other;
    
            }

        }
        
        
        return response()->json($response_array,$response_code);
    }

}