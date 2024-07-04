@extends('layouts.app')

@section('after-styles')

<style type="text/css">
	body {
		color: #fff;
	}
	.form-control {
        min-height: 41px;
		background: #fff;
		box-shadow: none !important;
		border-color: #e3e3e3;
	}
	.form-control:focus {
		border-color: #EF7E3D;
	}
    .form-control, .btn {        
        border-radius: 2px;
    }
	.login-form {
		width: 420px;
		margin: 0 auto;
		padding: 100px 0 30px;		
	}
	.login-form form {
		color: #7a7a7a;
		border-radius: 2px;
    	margin-bottom: 15px;
        font-size: 13px;
        background: #ececec;
        box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
        padding: 30px;	
        position: relative;	
    }
	.login-form h2 {
		font-size: 22px;
        margin: 35px 0 25px;
    }
	.login-form .avatar {
		position: absolute;
		margin: 0 auto;
		left: 0;
		right: 0;
		top: -50px;
		width: 95px;
		height: 95px;
		border-radius: 50%;
		z-index: 9;
		background: #f26f23;
		padding: 15px;
		box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.1);
	}
	.login-form .avatar img {
		width: 100%;
	}	
    .login-form input[type="checkbox"] {
        margin-top: 2px;
    }
    .login-form .btn {        
        font-size: 16px;
        font-weight: bold;
		background: #f26f23;
		border: none;
		margin-bottom: 20px;
    }
	.login-form .btn:hover, .login-form .btn:focus {
		background: #FF6208;
        outline: none !important;
	}    
	.login-form a {
		color: #fff;
		text-decoration: underline;
	}
	.login-form a:hover {
		text-decoration: none;
	}
	.login-form form a {
		color: #7a7a7a;
		text-decoration: none;
	}
	.login-form form a:hover {
		text-decoration: underline;
	}
</style>

@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="login-form">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="avatar">
                        <img src="{{ asset('images/avatar.png') }}" alt="Avatar">
                    </div>
                    <h2 class="text-center">Login</h2> 

                    <div class="form-group">

                        <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" placeholder="Username" value="{{ old('username') }}" required autocomplete="username" autofocus>

                        @error('username')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">

                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Password" required="required" autocomplete="current-password">

                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div> 

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-lg btn-block">
                            {{ __('Login') }}
                        </button>

                        @if (Route::has('password.request'))
                            <a class="btn btn-link" href="{{ route('password.request') }}">
                                {{ __('Forgot Your Password?') }}
                            </a>
                        @endif
                    </div>

                    <div class="form-group ">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                            <label class="form-check-label" for="remember">
                                {{ __('Remember Me') }}
                            </label>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
