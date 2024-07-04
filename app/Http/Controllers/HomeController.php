<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $canCreateToken = $this->checkTokenCreateAbility();

        $user = Auth::user();
        return view('home', compact('canCreateToken'));
    }

    
    public function checkTokenCreateAbility()
    {
        $canCreateToken = false;
        $user = auth()->user();

        if ($user->oauth_client)
            $canCreateToken = ($user->oauth_client
                        ->where('revoked', 0)
                        ->count()>0)? 
                        false : true;

        return $canCreateToken;
    }
}
