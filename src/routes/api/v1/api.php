<?php

use Illuminate\Support\Facades\Route;

$namespaceV1 = 'App\\Http\\Controllers\\API\\V1';

Route::group(['middleware' => ['auth:admin', 'passport.guard.admin']], function () {
    Route::post('/client', 'Laravel\Passport\Http\Controllers\ClientController@store')->name('clients.store');
    Route::get('/client', 'Laravel\Passport\Http\Controllers\ClientController@forUser')->name('clients.all');
    Route::put('/client/{client}', 'Laravel\Passport\Http\Controllers\ClientController@update')->name('clients.update');
    Route::delete('/client/{client}', 'Laravel\Passport\Http\Controllers\ClientController@destroy')->name('clients.delete');
});
Route::group(['namespace' => '\App\Http\Controllers\API\V1\\'], function () {
    Route::group(['middleware' => 'auth:api'], function () {
        //user auth
        Route::get('/user', 'Auth\User\AuthController@getUser')->middleware('auth:api')->name('user.info');
        Route::post('/user/logout', 'Auth\User\AuthController@logout')->middleware('auth:api')->name('user.logout');
        //post operations
        Route::get('/post', 'PostController@index')->name('post.index');
        Route::post('/post', 'PostController@store')->name('post.store');
        Route::delete('/post/permanent/{id}', 'PostController@forceDelete')->name('post.forceDelete');
        Route::get('/post/restore/{id}', 'PostController@restore')->name('post.restore');
        Route::get('/post/{post}', 'PostController@show')->name('post.show');
        Route::put('/post/{post}', 'PostController@update')->name('post.update');
        Route::delete('/post/{post}', 'PostController@delete')->name('post.delete');
        Route::get('/user/posts', 'PostController@getUserPosts')->name('user.posts');
        //Comments
        Route::get('/comment/restore/{post}/{comment}', 'CommentController@restore')->name('comment.restore');
        Route::post('/comment/{post}', 'CommentController@store')->name('comment.store');
        Route::delete('/comment/permanent/{post}/{id}', 'CommentController@forceDelete')->name('comment.forceDelete');
        Route::delete('/comment/{post}/{comment}', 'CommentController@delete')->name('comment.delete');
        Route::put('/comment/{post}/{comment}', 'CommentController@update')->name('comment.update');
        //post-likes
        Route::post('/like/post/{post}', 'LikePostController@storeLike')->name('like.post.save');
        Route::delete('/like/post/{post}', 'LikePostController@removeLike')->name('like.post.remove');
        //comment-likes
        Route::post('/like/comment/{comment}', 'LikeCommentController@storeLike')->name('like.comment.save');
        Route::delete('/like/comment/{comment}', 'LikeCommentController@removeLike')->name('like.comment.remove');
        //Reply
        Route::get('/reply/restore/{comment}/{reply}', 'ReplyController@restore')->name('reply.restore');
        Route::post('/reply/{comment}', 'ReplyController@store')->name('reply.store');
        Route::delete('/reply/permanent/{comment}/{reply}', 'ReplyController@forceDelete')->name('reply.forceDelete');
        Route::delete('/reply/{comment}/{reply}', 'ReplyController@delete')->name('reply.delete');
        Route::put('/reply/{comment}/{reply}', 'ReplyController@update')->name('reply.update');
        //Reply-likes
        Route::post('/like/reply/{reply}', 'LikeReplyController@storeLike')->name('like.reply.save');
        Route::delete('/like/reply/{reply}', 'LikeReplyController@removeLike')->name('like.reply.remove');
        //user-relations
        Route::post('/user/add-friend-request/{user}', 'RelationController@addFriend')->name('user.addFriend');
        Route::post('/user/approve-friend-request/{user}', 'RelationController@approveFriendRequest')->name('user.approveFriendRequest');
        Route::get('/user/friend-requests-list', 'RelationController@getFriendsRequestsList')->name('user.getFriendsRequestsList');
        Route::get('/user/friends-list', 'RelationController@friendList')->name('user.getFriendsList');
        Route::post('/user/reject-friend-request/{user}', 'RelationController@rejectFriend')->name('user.rejectFriend');
        Route::post('/user/remove-friend/{user}', 'RelationController@removeFriend')->name('user.removeFriend');
        //users-blocking
        Route::post('/user/block/{user}', 'RelationController@blockUser')->name('user.block');
        Route::delete('/user/block/{user}', 'RelationController@unblockUser')->name('user.unblock');
        Route::get('/user/block-list/', 'RelationController@getBlocksList')->name('user.blockList');
    });
    Route::group(['middleware' => 'guest:api'], function () {
        Route::post('/register', 'Auth\User\AuthController@register')->name('user.register');
        Route::post('/login', 'Auth\User\AuthController@login')->name('user.login');
    });
});
Route::group(['middleware' => ['passport.guard.api']], function () {
    Route::get('/oauth/tokens', 'Laravel\Passport\Http\Controllers\AuthorizedAccessTokenController@forUser');
    Route::get('/token/refresh', 'Laravel\Passport\Http\Controllers\TransientTokenController@refresh');
});
