<?php


// Welcome Page
Route::get('/', 'WelcomeController@index');

// Auth Routes
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

// Dashboard Page
Route::get('/dashboard', 'HomeController@index')->name('dashboard');

/*
 * Admin Routes
 */
Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['role:admin', 'auth']], function () {

    // IDP-Client Management
    Route::get('/users', 'UserController@index')->name('user.index');
    Route::get('/datatable-users', 'UserController@getUsersData')->name('users.datatable');
    Route::get('/users/create', 'UserController@create')->name('user.create');
    Route::post('/users/store', 'UserController@store')->name('user.store');
    Route::get('/users/edit/{user}', 'UserController@edit')->name('user.edit');
    Route::put('/users/update/{user}', 'UserController@update')->name('user.update');
    Route::get('/users/scope/{user}', 'UserController@addScope')->name('user.scope.add');
    Route::post('/users/scope/{user}', 'UserController@saveScope')->name('user.scope.save');

    // Customer Management
    Route::get('/customers', 'CustomerController@index')->name('customer.index');
    Route::get('/datatable-customers', 'CustomerController@getCustomersData')->name('customers.datatable');
    Route::get('/customers/create', 'CustomerController@create')->name('customer.create');
    Route::post('/customers/store', 'CustomerController@store')->name('customer.store');
    Route::get('/customers/edit/{customer}', 'CustomerController@edit')->name('customer.edit');
    Route::put('/customers/update/{customer}', 'CustomerController@update')->name('customer.update');

    // Scope Management
    Route::get('/scopes', 'ScopeController@index')->name('scope.index');
    Route::get('/scopes/create', 'ScopeController@create')->name('scope.create');
    Route::post('/scopes/store', 'ScopeController@store')->name('scope.store');
    Route::get('/scopes/edit/{scope}', 'ScopeController@edit')->name('scope.edit');
    Route::put('/scopes/{scope}', 'ScopeController@update')->name('scope.update');
    Route::delete('/scopes/{scope}', 'ScopeController@destroy')->name('scope.delete');


    Route::get('user-data-entry', 'DataMigrationController@create');
    Route::post('user-data', 'DataMigrationController@uploadUserDataByExcel')->name('user-data.save');

});



/**
 * Passport Custom Routes
 */
Route::group(['namespace' => 'Passport', 'as' => 'passport.', 'middleware' => 'role:idp-client'], function () {

    Route::post('/oauth/clients', 'ClientController@store');
});

