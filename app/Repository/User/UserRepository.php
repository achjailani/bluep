<?php
namespace App\Repository\User;

use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserRepository {
    
    public function __construct(User $model) 
    {
        $this->model = $model;
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
        } catch (\Throwable $th) {
            return ['status' => false, 'message' => $th->getMessage()];
        }
    }

}