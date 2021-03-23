<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function apiUnprocessableEntityResponse($errors = []) 
    {
    	return response()->json([
    		"status_code" => 422,
    		"data" => [
    			"error" => [
    				"messages" => $errors
    			]
    		], 
    	], 422);
    }

    public function restApi($data, $many = false, $message = 'Ok', $code = 200) 
    {
        $attribute = $many ? array('items' => $data) : $data;
        return response([
            'status_code'   => $code,
            'message'       => $message,
            'data'          => $attribute
        ], $code);
    }

    public function apiInternalServerErrorResponse($errors = []) {
    	return response()->json([
    		"status_code" => 500,
    		"data"		  => [
    			"error" => [
    				"messages" => $errors
    			]
    		]
    	], 500);
    }
}
