<?php

use Illuminate\Http\Request;
use Laravel\Passport\Token;

/**
 * Api routes
 */
Route::group(['namespace' => 'Api'/*, 'middleware' => ['log.request']*/], function () {

    Route::post('/logout', 'LoginController@logout')->middleware('auth:api');

    Route::post('/check/client/token', 'AuthCheckingController@checkClient')->middleware('client');
    Route::post('/check/client/authorization', 'AuthCheckingController@checkClientAuthorization')->middleware('client');

    Route::post('/check/client-customer/authorization', 'AuthCheckingController@checkCusClientAuthorization')->middleware('client');

    Route::post('/check/user/token', 'AuthCheckingController@checkPasswordGrantToken')->middleware('client');

    Route::get('/check-authorization', function () {
        $data = new \stdClass();
        $data->status = 'SUCCESS';
        $data->status_code = 200;
        $data->token_status = 'Valid';
        $data->token_user = 'User';

        $authUser = new \stdClass();
        $authUser->id = auth()->user()->id;
        $authUser->mobile = auth()->user()->mobile;
        $authUser->msisdn = auth()->user()->msisdn;
        $authUser->email = auth()->user()->email;

        $data->user = $authUser;

        return response()->json($data);
    })->middleware('auth:api');

    Route::get('/check-client-token', function () {
        $data = new \stdClass();
        $data->status = 'SUCCESS';
        $data->status_code = 200;
        $data->token_status = 'Valid';
        $data->token_user = 'Client';

        return response()->json($data);
    })->middleware('client');

    Route::post('customers', 'CustomerController@store')->middleware('client');
    Route::put('customers/{id}', 'CustomerController@update')->middleware('client');
    Route::put('customers/forget/password', 'CustomerController@forgetPassword')->middleware('client');
    Route::put('customers/change/password', 'CustomerController@changePassword')->middleware('client');
    Route::get('/customers/{msisdn}', 'CustomerController@show')->middleware('client');
});

Route::group(['prefix' => '/v1', 'namespace' => 'Api'/*, 'middleware' => ['log.request']*/], function () {

    Route::post('/logout', 'LoginController@logout')->middleware('auth:api');

    Route::post('/check/client/token', 'AuthCheckingController@checkClient')->middleware('client');
    Route::post('/check/client/authorization', 'AuthCheckingController@checkClientAuthorization')->middleware('client');

    Route::post('/check/client-customer/authorization', 'AuthCheckingController@checkCusClientAuthorization')->middleware('client');

    Route::post('/check/user/token', 'AuthCheckingController@checkPasswordGrantToken')->middleware('client');

    Route::get('/check-authorization', function () {
        $data = new \stdClass();
        $data->status = 'SUCCESS';
        $data->status_code = 200;
        $data->token_status = 'Valid';
        $data->token_user = 'User';

        $authUser = new \stdClass();
        $authUser->id = auth()->user()->id;
        $authUser->mobile = auth()->user()->mobile;
        $authUser->msisdn = auth()->user()->msisdn;
        $authUser->email = auth()->user()->email;
        $authUser->is_password_set = auth()->user()->is_password_set;
        $authUser->name = auth()->user()->name;
        $authUser->first_name = auth()->user()->first_name;
        $authUser->last_name = auth()->user()->last_name;
        $authUser->birth_date = auth()->user()->birth_date;
        $authUser->address = auth()->user()->address;
        $authUser->gender = auth()->user()->gender;
        $authUser->alternate_phone = auth()->user()->alternate_phone;
        $authUser->profile_image = auth()->user()->profile_image_base64;// ? config('filesystems.profile_image_path').auth()->user()->profile_image : null;


        $data->user = $authUser;

        return response()->json($data);
    })->middleware('auth:api');

    Route::get('/check-client-token', function () {
        $data = new \stdClass();
        $data->status = 'SUCCESS';
        $data->status_code = 200;
        $data->token_status = 'Valid';
        $data->token_user = 'Client';

        return response()->json($data);
    })->middleware('client');

    //Route::post('/logout', 'CustomerController@logout')->middleware('auth:api');

    Route::post('customers', 'CustomerController@store')->middleware('client');
    Route::post('/customers/update/perform', 'CustomerController@update')->middleware('auth:api');
    Route::get('/customers/profile/photo/remove', 'CustomerController@removeProfilePhoto')->middleware('auth:api');
    Route::post('/customers/profile/photo/set', 'CustomerController@setProfilePhoto')->middleware('auth:api');
    Route::post('/customers/set/password', 'CustomerController@setPassword')->middleware('auth:api');
    Route::put('customers/forget/password', 'CustomerController@forgetPassword')->middleware('client');
    Route::put('customers/change/password', 'CustomerController@changePassword')->middleware('client');
    Route::get('/customers/{msisdn}', 'CustomerController@show')->middleware('client');
    Route::get('/customers/basic-info/{msisdn}', 'CustomerController@getCustomerInfoWithoutProfileImage')->middleware('client');
    Route::get('/customers/profile-image/{msisdn}', 'CustomerController@getCustomerProfileImage')->middleware('client');
    Route::get('/customers/otp/{msisdn}', 'CustomerController@getCustomerOtp');
    Route::post('/customers/reset/password/request', 'CustomerController@resetPasswordRequest')->middleware('client');
    Route::post('/customers/reset/password/perform', 'CustomerController@resetPassword')->middleware('client');
    Route::get('/customers/switch-account/{msisdn}', 'CustomerController@show');
    Route::post('/customers/switch-account/update/perform', 'CustomerController@update');
    Route::get('/customers/switch-account/basic-info/{msisdn}', 'CustomerController@getCustomerInfoWithoutProfileImage');
    Route::get('/customers/switch-account/profile-image/{msisdn}', 'CustomerController@getCustomerProfileImage');
    Route::post('/customers/update-profile', 'CustomerController@updateProfileInfo');
    Route::post('/customers/update-image', 'CustomerController@updateProfileImage');


    Route::get('/test', function () {
        $data = new \stdClass();
        $data->status = 'SUCCESS';
        $data->status_code = 200;
        $data->token_status = 'Valid';
        $data->token_user = 'Client';

        return response()->json($data);
    });

/*
    Route::post('image/test', function (Request $request){

        $file = $request->file('profile_photo');
        $ext = $file->getClientOriginalExtension();
        $photoExt = array('jpg', 'JPG', 'JPEG', 'jpeg', 'png', 'PNG', 'gif', 'bmp');
        if (!in_array($ext, $photoExt)) {
            return ['status' => 'error', 'message' => 'Invalid image extension'];
        }
        $path = base64_encode(file_get_contents($file));

        $user = \App\Models\User::find(1);

        $user->update([
            'profile_image_base64' => $path
        ]);
        return ['status' => 'success', 'path' => $path];
    });*/

/*     Route::get('test/token', function (){

         $customer = \App\Models\User::find(173);

         $tokens = Token::where('user_id', 17322)->pluck('id')->toArray();

         dd($tokens);
     });*/
});
