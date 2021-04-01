<?php
namespace App\Repository\User;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;

class UserRepository {
    
    public function __construct(User $model) 
    {
        $this->model = $model;
    }

    public function login(array $data)
    {
        try {
            if(Auth::attempt(['email'  => $data['email'], 'password' => $data['password']])){
                $user = Auth::user();
                $success['user']  = Auth::user();
                $accessToken      = $user->createToken($data['email']);
                $token            = $accessToken->token;
    
                if($data['remember_me'] == true) {
                    $token->expires_at = Carbon::now()->addWeeks(1);
                }
                $success['token']      = $accessToken->accessToken;
                $success['token_type'] = "Bareer";
                $success['expires_at'] = Carbon::parse($accessToken->token->expires_at)->toDateTimeString();

                return ['status' => true, 'data' => $user, 'message' => $success];
            }
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function call($data, $id = null) 
    {
        try {
            $model = ($id === null) ? new User() : User::find($id);
            $model->name        = $data['name'];
            $model->email       = $data['email'];
            $model->password    = Hash::make($data['password']);
            $model->save(); 
            return ['status' => true, 'data' => $model, 'message' => ($id == true) ? 'Data successfull created' : 'Data successfully updated'];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function getSingle($id) 
    {
        try {
            $model = $this->model->find($id);
            return ['status' => true, 'data' => $model, 'message' => 'ok'];
        } catch (\Exception $th) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }
}