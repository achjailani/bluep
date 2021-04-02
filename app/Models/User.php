<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    /**
     * Get profile for user 
     */
    public function profile() 
    {
        return $this->hasOne(UserProfile::class, 'user_id', 'id');    
    }

    public function hasVerifiedEmail() 
    {
        return $this->email_verified_at === null ? false : true;
    }
    /**
     * Get roles for user
     */
    public function role() 
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    /**
     * Get blogs for user
     */
    public function blogs() 
    {
        return $this->hasMany(Blog::class, 'user_id', 'id');
    }

    /**
     * Get projects for user
     */
    public function projects() 
    {
        return $this->hasMany(Project::class, 'user_id', 'id');
    }

    /**
     * Get researches for user
     */
    public function researches() 
    {
        return $this->hasMany(Research::class, 'user_id', 'id');
    }
}
