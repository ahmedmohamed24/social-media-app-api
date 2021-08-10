<?php

use Illuminate\Support\Facades\Route;

$namespaceV1 = 'App\\Http\\Controllers\\API\\V1';
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/client/register', [\App\Http\Controllers\API\V1\Auth\Client\RegisterController::class, 'storeClient'])->middleware('auth')->name('clients.store');

Route::group(['namespace' => '\App\Http\Controllers\API\V1\\'], function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('/user', 'Auth\User\AuthController@getUser')->middleware('auth:api')->name('user.info');
        Route::post('/user/logout', 'Auth\User\AuthController@logout')->middleware('auth:api')->name('user.logout');
        Route::post('/post', 'PostController@store')->name('post.store');
        Route::delete('/post/permanent/{id}', 'PostController@forceDelete')->name('post.forceDelete');
        Route::get('/post/restore/{id}', 'PostController@restore')->name('post.restore');
        Route::get('/post/{post}', 'PostController@show')->name('post.show');
        Route::put('/post/{post}', 'PostController@update')->name('post.update');
        Route::delete('/post/{post}', 'PostController@delete')->name('post.delete');
        Route::get('/user/posts', 'PostController@getUserPosts')->name('user.posts');
    });
    Route::group(['middleware' => 'guest:api'], function () {
        Route::post('/register', 'Auth\User\AuthController@register')->name('user.register');
        Route::post('/login', 'Auth\User\AuthController@login')->name('user.login');
    });
});
