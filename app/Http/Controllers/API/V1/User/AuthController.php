<?php

namespace App\Http\Controllers\API\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use App\Repository\User\UserRepository;

class AuthController extends Controller
{
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function login(Request $request)
    {
        $validation = $this->validateLogin($request->all());
        if($validation->fails()) {
            return $this->apiUnprocessableEntityResponse($validation->errors());
        }
        $response = $this->repository->login($request->all());
        if($response['status'] === true) {
            return $this->restApi($response['message']);
        }
        return $this->apiInternalServerErrorResponse($response['message']);
    }

    public function register(Request $request)
    {
        $validation = $this->validator($request->all());
        if($validation->fails()) {
            return $this->apiUnprocessableEntityResponse($validation->errors());
        } 

        $response = $this->repository->call($request->all());
        if($response['status'] === true) {
            $response['data']->sendEmailVerificationNotification();
            return $this->restApi($response['data'], false, $response['message']);
        }
        return $this->apiInternalServerErrorResponse($response['message']);
    }

    public function validator($data, $id = null) 
    {
        $email = ($id == null) ? null : '|unique:users';
        return Validator::make($data, [
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|max:255'.$email,
            'password'  => 'required|min:8|confirmed',
            'password_confirmation' => 'required|min:8'
        ]);
    }

    public function validateLogin($data)
    {
        return Validator::make($data, [
            'email'     => 'required|email',
            'password'  => 'required|min:8'
        ]);
    }
}
