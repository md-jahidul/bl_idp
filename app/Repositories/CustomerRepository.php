<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\Request;

/**
 * Class CustomerRepository
 * 
 * @package App\Repositories
 */
class CustomerRepository
{
    /**
     * @var Customer
     */
    protected $model;

    /**
     * CustomerRepository constructor.
     * 
     * @param User $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * Get Customers Info
     *
     * @return mixed
     */
    public function getAll()
    {
        return $this->model->where('user_type', 'CUSTOMER')->get();
    }

    /**
     * Store Customer
     *
     * @param App\Http\Requests\StoreCustomerRequest $request
     * 
     * @return App\Models\Customer $customer
     */
    public function save(StoreUserRequest $request)
    {
        $pass = ($request->has('password'))? $request->has('password'): '12345678';

        $customer = new User();
        $customer->name = $request->input('name');
        $customer->username = $request->input('mobile');
        $customer->user_type = $request->input('user_type');
        $customer->email = $request->input('email');
        $customer->mobile = $request->input('mobile');
        $customer->msisdn = '88'.$request->input('mobile');
        $customer->password = Hash::make($pass);        
        $customer->password_grant = NULL;
        $customer->status = User::ACTIVE;
        $customer->save();

        return $customer;
    }

    /**
     * Update Customer
     * 
     * @param App\Models\User $customer
     * 
     * @param Illuminate\Http\Request $request
     * 
     * @return App\Models\User $customer
     */
    public function update(User $customer, Request $request)
    {
        $customer->name = $request->input('name');
        $customer->username = $request->input('mobile');
        $customer->email = $request->input('email');
        $customer->mobile = $request->input('mobile');
        $customer->save();

        return $customer;
    }

}
