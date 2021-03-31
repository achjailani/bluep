<?php

namespace App\Http\Controllers\API\V1\User;

use App\Http\Controllers\Controller;
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
        if(!$model->hasVerfiedEmail()) {
            $model->markEmailAsVerified();
        }

        return redirect()->to('/');
    }

    public function resend() 
    {
        $model = $this->model->find(3);
        if($model->hasVerifiedEmail()) {
            return response([]);
        }
        $model->sendEmailVerificationNotification();
        return response([]);
    }
}
