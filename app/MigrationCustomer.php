<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MigrationCustomer extends Model
{
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
}
