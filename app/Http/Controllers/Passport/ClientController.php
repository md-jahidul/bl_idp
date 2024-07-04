<?php

namespace App\Http\Controllers\Passport;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClientController extends \Laravel\Passport\Http\Controllers\ClientController
{
    
    /**
     * Store a new client.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Laravel\Passport\Client
     */
    public function store(Request $request)
    {
        $this->validation->make($request->all(), [
            'name' => 'required|max:255',
            // 'redirect' => ['required', $this->redirectRule],
        ])->validate();

        return $this->clients->create(
            $request->user()->getKey(), $request->name, $request->redirect, false, $request->user()->password_grant
        )->makeVisible('secret');
    }
}
