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

    public function forgotPassword(Request $request) 
    {
        $validation = Validator::make($request->all(),[
            'email' => 'required|email'
        ]);

        if($validation->fails()) {
            return $this->apiUnprocessableEntityResponse($validation->errors());
        }

        try {
            $status = Password::sendResetLink($request->only('email'));
            if($status === Password::RESET_LINK_SENT) {
                return response([
                    'status_code'   => 200,
                    'message'       => 'We\'ve sent you reset password link, please check your email'
                ], 200);
            }
        } catch (\Exception $e) {
            return $this->apiInternalServerErrorResponse($e->getMessage());
        }
    }

    public function getTokenResetPassword($token) {
        return response([
            'status_code'   => 200,
            'token'         => $token
        ], 200);

        
    }

    public function resetPassword(Request $request) {
        $validation = Validator::make($request->all(),[
            'email'     => 'required|email',
            'token'     => 'required',
            'password'  => 'required|min:8|confirmed'
        ]);

        if($validation->fails()) {
            return $this->apiUnprocessableEntityResponse($validation->errors());
        }

        try {
            $reset = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function($user, $password) use ($request) {
                    $user->forceFill([
                        'password'  => Hash::make($password)
                    ])->setRememberToken(Str::random(60));

                    $user->save();
                    event(new PasswordReset($user));
                }
            );

            if($reset === Password::PASSWORD_RESET) {
                return response([
                    'status_code'   => 200,
                    'message'       => 'Password has been reseted successfully, please login'
                ], 200);
            }
        } catch (\Exception $e) {
            return $this->apiInternalServerErrorResponse($e->getMessage());
        }
        
    } 

    /**
     * Validate user before registered
     * @param array $data 
     */
    public function validator($data) 
    {
        $email = ($id == null) ? null : '|unique:users';
        return Validator::make($data, [
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|max:255'.$email,
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
