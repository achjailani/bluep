<?php

namespace App\Http\Controllers\API\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;

class VerificationController extends Controller
{
    public function __construct(User $model) 
    {
        $this->model = $model;
    }

    public function verify($id, Request $request) 
    {
        $model = $this->model->find($id);
        if(!$model->hasVerifiedEmail()) {
            $model->markEmailAsVerified();
        }

        return redirect()->to('/');
    }

    public function resend() 
    {
        $auth = Auth::guard('api');
        if($auth->check()){
            if($auth->user()->hasVerifiedEmail()) {
                return response([
                    'status_code' => 200,
                    'message'     => 'Your email was verified'
                ], 200);
            }
            $auth->user()->sendEmailVerificationNotification();
            return response([
                'status_code'   => 200,
                'message'       => 'We\'ve sent you email verification, please check your email'
            ], 200);
        }
    }

    public function verifyEmail() {
        return response([
            'status_code'   => 422,
            'message'       => 'Please confirm your account, check your email.\n or resend if you don\'t receive email verification, click this '.route('verification.resend')
        ], 422);
    }
}
