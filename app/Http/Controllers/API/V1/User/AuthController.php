<?php

namespace App\Http\Controllers\API\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Repository\User\UserRepository;

class AuthController extends Controller
{
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Login process for user
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response  
     */
    public function login(Request $request)
    {
        $validation = $this->validateLogin($request->all());
        if($validation->fails()) {
            return $this->apiUnprocessableEntityResponse($validation->errors());
        }
        $response = $this->repository->login($request->all());
        if($response['status'] === true) {
            $this->middleware('verified');
            return $this->restApi($response['message']);
        }
        return $this->apiInternalServerErrorResponse($response['message']);
    }

    /**
     * Register process for new user
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response  
     */
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

    /**
     * Send email forgot password
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response  
     */
    public function forgotPassword(Request $request) 
    {
        $validation = Validator::make($request->all(),[
            'email' => 'required|email'
        ]);

        if($validation->fails()) {
            return $this->apiUnprocessableEntityResponse($validation->errors());
        }

        $response = $this->repository->sendEmailforgotPassword($request->all());
        if($response['status'] === true) {
            return $this->restApi($response['data'], false, $response['message']);
        }
        
        return $this->apiInternalServerErrorResponse($response['message']);
    }

    /**
     * Get token from mail server
     * @param string $token
     * @return \Illuminate\Http\Response  
     */
    public function getTokenResetPassword($token) {
        return response([
            'status_code'   => 200,
            'token'         => $token
        ], 200);
    }

    /**
     * Reset Password
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response  
     */
    public function resetPassword(Request $request) {
        $validation = Validator::make($request->all(),[
            'email'     => 'required|email',
            'token'     => 'required',
            'password'  => 'required|min:8|confirmed'
        ]);

        if($validation->fails()) {
            return $this->apiUnprocessableEntityResponse($validation->errors());
        }

        $response = $this->repository->updateToNewPassword($request->all());
        if($response['status'] === true) {
            return $this->restApi($response['data'], false, $response['message']);
        }
        return $this->apiInternalServerErrorResponse($response['message']);
    } 

    /**
     * Validate user before registered
     * @param array $data 
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

    /**
     * Validate user before login
     * @param array $data
     */
    public function validateLogin($data)
    {
        return Validator::make($data, [
            'email'     => 'required|email',
            'password'  => 'required|min:8'
        ]);
    }
}
