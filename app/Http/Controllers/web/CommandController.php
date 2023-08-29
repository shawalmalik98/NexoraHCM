<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class CommandController extends Controller
{
    //
    public function clear_all_cache(){
        Artisan::call('cache:clear');
        dd("Successfully, you have cleared all cache of application.");
    }

    public function clear_all_config(){
        Artisan::call('config:clear');
        dd("Successfully, you have cleared all config of application.");
    }

    public function clear_all_route(){
        Artisan::call('route:clear');
        dd("Successfully, you have cleared all route of application.");
    }


    public function clear_all_view(){
        Artisan::call('view:clear');
        dd("Successfully, you have cleared all view of application.");
    }

    public function truncate_migration(){
        $truncate = DB::table('migrations')->truncate();
      
        return response()->json([
            'status' => 'TRUE',
            'msg' => 'Function Executed.'
        ]);
        
    }

}
