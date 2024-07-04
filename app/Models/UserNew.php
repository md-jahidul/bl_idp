<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UserNew extends Authenticatable
{
    use Notifiable, HasApiTokens, HasRoles;

    /**
     * Constants
     *
     */
    const ACTIVE = 1;
    const INACTIVE = 0;


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users_new';



    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'address',
        'birth_date',
        'profile_image',
        'profile_image_base64',
        'username',
        'msisdn',
        'email',
        'mobile',
        'password',
        'status',
        'user_type',
        'is_password_set',
        'gender',
        'alternate_phone'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function oauth_client()
    {
        return $this->hasOne(\App\Models\Passport\Client::class, 'user_id', 'id');
    }

    public function findForPassport($username)
    {
        return $this->where('username', $username)->first();
    }

    public function clientScopes()
    {
        return $this->belongsToMany(Scope::class, 'client_scope', 'client_user_id', 'scope_id');
    }

}
