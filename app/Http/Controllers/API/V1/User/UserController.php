<?php

namespace App\Http\Controllers\API\V1\User;

use App\Http\Controllers\Controller;
use App\Repository\User\UserRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(UserRepository $repository) 
    {
        $this->repository = $repository;
    }

    public function profile($username = null) {
        $response = $this->repository->getSingleUserByUsername($username);
        if($response['status'] === true) {
            return $this->restApi($response['data'], false, $response['message']);
        }
        return $this->apiInternalServerErrorResponse($response['message']);
    }

    public function get($id)
    {
        $response = $this->repository->getSingle($id);
        if($response['status'] === true) {
            return $this->restApi($response['data'], false, $response['message']);
        }
        return $this->apiInternalServerErrorResponse($response['message']);
    }

    public function save(Request $request, $id = null)
    {
        $validation = $this->validator($request->all(), $id);
        if($validation->fails()) {
            return $this->apiUnprocessableEntityResponse($validation->errors());
        }
        
        $response = $this->repository->call($request->all(), $id);
        if($response['status'] === true) {
            $response['data']->sendEmailVerificationNotification();
            return $this->restApi($response['data'], false, $response['message']);
        }

        return $this->apiInternalServerErrorResponse($response['message']);
    }

    /**
     * Validate user before stored
     * @param array $data
     * @param $id 
     */
    public function validator($data) 
    {
        return Validator::make($data, [
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|max:255|unique:users',
            'username'  => 'required|alpha_dash|max:255|unique:users',
            'password'  => 'required|min:8|confirmed',
            'password_confirmation' => 'required|min:8'
        ]);
    }
}
