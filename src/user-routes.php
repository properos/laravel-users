<?php

use Properos\Base\Classes\Helper;
use Illuminate\Support\Facades\Route;

$prefix = config('properos_users.router.default.prefix');
$middleware = config('properos_users.router.default.middleware');
$namespaces = config('properos_users.router.default.namespaces');

Route::namespace($namespaces)->middleware('web')->prefix(Helper::getValue($prefix,'web','/'))->group(function() use ($middleware){
    Route::get('/login', 'LoginController@showLoginForm')->name('login');
    Route::post('/login', 'LoginController@login');
    Route::match(['get','post'],'/logout', 'LoginController@logout')->name('logout');
    Route::post('/password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('/password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('/password/reset', 'ResetPasswordController@reset');
    Route::get('/password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
    Route::get('/register', 'RegisterController@showRegistrationForm')->name('register');
    Route::post('/register', 'RegisterController@register');

    Route::get('auth/{provider}', 'SocialRegisterController@redirectToProvider');
    Route::get('auth/{provider}/callback', 'SocialRegisterController@handleProviderCallback');
});

Route::namespace($namespaces)->middleware('web')->prefix(Helper::getValue($prefix,'admin_web','/'))->group(function() use ($middleware){
    Route::get('/ru', 'UserController@returnUser');
    Route::group(['middleware' => 'auth'], function () use($middleware) {
        Route::get('/my-profile', 'UserController@index');
        Route::get('/users/{path?}/{path1?}/{path2?}/{path3?}/{path4?}', 'UserController@index')->middleware(Helper::getValue($middleware, 'admin', Helper::getValue($middleware, 'private', [])));
    });
});

Route::namespace($namespaces)->middleware('web')->prefix(Helper::getValue($prefix,'admin_web_api','/'))->group(function() use ($middleware){
    Route::get('/su/{user_id}', 'UserController@setUser');
    Route::group(['middleware' => 'auth'], function () use($middleware) {
        Route::get('/my-profile/edit', 'UserController@showProfile');
        Route::post('/my-profile/change-password', 'UserController@changeMyPassword');
        Route::match(['post','put'],'/my-profile/update', 'UserController@updateProfile');

        Route::group(['prefix' => '/users'], function () use($middleware) {
            Route::post('/search', 'UserController@list');
            Route::post('/{restrictable}/search', 'UserController@getRestrictableList');
            
            Route::group(['middleware' => Helper::getValue($middleware, 'admin', Helper::getValue($middleware, 'private', []))], function () {
                Route::get('/{id}', 'UserController@show')->where(['id'=> '[1-9][0-9]*']);
                Route::post('/', 'UserController@create');
                Route::match(['post','put'], '/{id}', 'UserController@update')->where(['id'=> '[1-9][0-9]*']);
                Route::match(['post','put'], '/{id}/change-password', 'UserController@changePassword')->where(['id'=> '[1-9][0-9]*']);
                Route::match(['post','delete'], '/{id}/delete', 'UserController@destroy')->where(['id'=> '[1-9][0-9]*']);
                Route::post('/{id}/role', 'UserController@assignRole')->where(['id'=> '[1-9][0-9]*']);
                Route::post('/{id}/role/delete', 'UserController@removeRole')->where(['id'=> '[1-9][0-9]*']);
                Route::post('/{id}/permission', 'UserController@assignPermission')->where(['id'=> '[1-9][0-9]*']);
                Route::post('/{id}/permission/delete', 'UserController@removePermission')->where(['id'=> '[1-9][0-9]*']);
                
                Route::post('/update-profile', 'ApiUserController@updateProfile');
                Route::post('/ledgers/search', 'ApiUserController@searchLedgers');
            });
        });
    });
});

Route::namespace($namespaces)->middleware('api')->prefix(Helper::getValue($prefix,'api','/'))->group(function() use ($middleware){
    Route::post('login', 'LoginController@apiLogin');
    Route::post('register', 'RegisterController@apiRegister');
    
    Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'ResetPasswordController@reset');

    Route::group(['prefix' => '', 'middleware' => 'auth:api'], function () {
        Route::post('logout', 'LoginController@apiLogout');
        Route::post('/update-profile', 'ApiUserController@updateProfile');
        Route::put('/change-user-password/{id}', 'ApiUserController@changeUserPassword');
    });
});







