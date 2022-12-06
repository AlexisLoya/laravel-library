<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function getResponse201(string $message, string $message2, $data){
        return [
            "error"=> false,
            "message"=> "$message was $message2",
            "data"=> $data
        ];
    }
    public function getResponse500($error){
        return [
            "error"=> true,
            "message"=> "Error!",
            "data"=> $error
        ];
    }
    public function getResponse401(){
        return [
            "error"=> true,
            "message"=> "method not found!",
            "data"=> ''
        ];
    }
    public function getResponse200($message){
        return [
            "error"=> true,
            "message"=> "profile",
            "data"=> $message
        ];
    }
    public function getResponse403()
    {
        return response()->json([
            'message' => "You do not have permission to access this resource"
        ], 403);
    }

}
