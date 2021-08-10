<?php

$namespaceV1 = 'App\\Http\\Controllers\\API\\V1\\';

Route::group(['namespace' => $namespaceV1, 'prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::group(['middleware' => 'guest:admin'], function () {
        Route::post('/login', 'Auth\Admin\AuthController@login')->name('login');
    });
    Route::group(['middleware' => 'auth:admin'], function () {
        Route::post('/logout', 'Auth\Admin\AuthController@logout')->name('logout');
        Route::get('/info', 'Auth\Admin\AuthController@getAdmin')->name('info');
    });
});
