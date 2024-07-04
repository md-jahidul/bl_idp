<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

/**
 * Class UserRepository
 * 
 * @package App\Repositories
 */
class UserRepository
{
    /**
     * @var User
     */
    protected $model;

    /**
     * UserRepository constructor.
     * 
     * @param User $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * Get Users Info
     *
     * @return mixed
     */
    public function getAll()
    {
        return $this->model->where('user_type', 'CLIENT')->get();
    }

    /**
     * Store User
     *
     * @param App\Http\Requests\StoreUserRequest $request
     * 
     * @return App\Models\User $user
     */
    public function save(StoreUserRequest $request)
    {
        $user = new User();
        $user->name = $request->input('name');
        $user->username = $request->input('email');
        $user->user_type = $request->input('user_type');
        $user->email = $request->input('email');
        $user->mobile = $request->input('mobile');
        $user->msisdn = '88'.$request->input('mobile');
        $user->password = Hash::make('12345678');
        $user->password_grant = $request->input('password_grant');
        $user->status = User::ACTIVE;
        $user->save();

        return $user;
    }

    /**
     * Update User
     * 
     * @param App\Models\User $user
     * 
     * @param App\Http\Requests\UpdateUserRequest $request
     * 
     * @return App\Models\User $user
     */
    public function update(User $user, UpdateUserRequest $request)
    {
        $user->name = $request->input('name');
        $user->username = $request->input('email');
        $user->email = $request->input('email');
        $user->mobile = $request->input('mobile');
        $user->msisdn = '88'.$request->input('mobile');
        $user->status = $request->input('status');
        $user->save();

        $user->oauth_client()->update(
            ['revoked' => !$request->input('status')]
        );

        return $user;
    }

}
