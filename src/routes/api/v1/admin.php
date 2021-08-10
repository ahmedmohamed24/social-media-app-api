<?php

$namespaceV1 = 'App\\Http\\Controllers\\API\\V1\\Admin\\';

Route::group(['namespace' => $namespaceV1, 'prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::group(['middleware' => 'guest:admin'], function () {
        Route::post('/login', 'AuthController@login')->name('login');
    });
    Route::group(['middleware' => 'auth:admin'], function () {
        Route::post('/logout', 'AuthController@logout')->name('logout');
        Route::get('/info', 'AuthController@getAdmin')->name('info');
    });
});
