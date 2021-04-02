<?php
namespace App\Repository\User;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use App\Repository\ImageUploader;
use Carbon\Carbon;
use App\Models\User;
use App\Models\UserProfile;

class UserRepository {
    
    public function __construct(User $model, ImageUploader $uploader) 
    {
        $this->model    = $model;
        $this->uploader = $uploader;
        $this->imageUrl = '/user/profile/';
    }

    /**
     * Login process for user
     * @param array $data
     */
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

    /**
     * Store or update user's data
     * @param array $data
     * @param int $id 
     */
    public function call($data, $id = null) 
    {
        try {
            $model = ($id === null) ? new User() : User::find($id);
            $model->name        = $data['name'];
            $model->email       = $data['email'];
            $model->username    = $data['username'];
            $model->password    = Hash::make($data['password']);
            $model->save(); 
            return ['status' => true, 'data' => $model, 'message' => ($id == true) ? 'Data successfull created' : 'Data successfully updated'];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function updateUserProfile(array $data, $id = null)
    {
        try {
            $model = ($id == null) ? Auth::user() : $this->model->find($id);
            $model->name = $data['name'];
            $model->username = $data['username'];
            $model->save();

            $profile    = $model->profile ? $model->profile : new UserProfile();
            $profile_id = $model->profile ? $model->profile->id : null;

            $profile->title     = $data['title'];
            $profile->bio       = $data['bio'];
            $profile->image     = $this->imageExection($data['image'], $profile_id);
            $profile->address   = $data['address'];
            $profile->linkedin  = $data['linkedin'];
            $profile->github    = $data['github'];
            $profile->instagram = $data['instagram'];
            $profile->facebook  = $data['facebook'];
            $model->profile()->save($profile);

            return ['status' => true, 'data' => $model,  'message' => 'Updated successfully'];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get single data from user
     * @param int $id 
     */
    public function getSingleUserById($id = null) 
    {
        try {
            $model = $this->model->find($id);
            return ['status' => true, 'data' => $model, 'message' => 'ok'];
        } catch (\Exception $th) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Send link reset password to user's email
     * @param array $data 
     */
    public function sendEmailforgotPassword(array $data) 
    {
        try {
            $status = Password::sendResetLink($data['email']);
            if($status === Password::RESET_LINK_SENT) {
                $message = 'We\'ve sent you reset password link, please check your email';
                return ['status' => true, 'message' => $message, 'data' => []];
            }
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Reset password
     * @param array $data 
     */
    public function updateToNewPassword(array $data) 
    {
        try {
            $reset = Password::reset(
                ['email', 'password', 'password_confirmation', 'token'],
                function($user, $password) use ($data) {
                    $user->forceFill([
                        'password'  => Hash::make($password)
                    ])->setRememberToken(Str::random(60));

                    $user->save();
                    event(new PasswordReset($user));
                }
            );

            if($reset === Password::PASSWORD_RESET) {
                $message = 'We\'ve sent you reset password link, please check your email';
                return ['status' => true, 'message' => $message, 'data' => []];
            }
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function imageExection($request, $id = null) 
    {
        $data = $request->all();

        $add_name = Str::random(15).'_'.time();
        $img_name = $this->imageUrl.$add_name.'.'.$data['image']->getClientOriginalExtension();

        if(!is_null($id)) {
            $model = UserProfile::find($id);
            if($request->hasFile('image')) {
                $this->uploader->up($data['image'], $this->imageUrl, $disk = 'public', $add_name);
                if(file_exists(storage_path('app/public').$model->image)) {
                    unlink(storage_path('app/public').$model->image);
                }
            } else {
                $img_name = $model->image;
            }
        } 

        if(is_null($id)) {
            $this->uploader->up($data['image'], $this->imageUrl, $disk = 'public', $add_name);
        }
        
    	return $img_name;
    }
}