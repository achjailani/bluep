<?php

namespace App\Http\Controllers\API\V1\User;

use App\Http\Controllers\Controller;
use App\Repository\User\UserRepository;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(UserRepository $repository) 
    {
        $this->repository = $repository;
    }

    public function get($id)
    {
        $response = $this->repository->getSingle($id);
        if($response['status'] === true) {
            return $this->restApi($response['data'], false, $response['message']);
        }
        return $this->apiInternalServerErrorResponse($response['message']);
    }

    
}
