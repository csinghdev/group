<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'api'], function() {
    Route::get('groups/{id}/users', 'UserController@index');
    Route::resource('users', 'UserController', ['only' => ['store']]);
    Route::get('users/{group}', 'UserController@index');

    Route::resource('groups', 'GroupController', ['only' => ['index', 'store', 'update']]);

    Route::get('posts/{id}/comments', 'CommentController@index');
    Route::post('posts/{id}/comments', 'CommentController@store');

    Route::get('groups/{id}/posts', 'PostController@index');
    //Route::get('groups/{group_id}/post/{post_id}', 'PostController@post');
    Route::get('groups/{id}/posts/{user}', 'PostController@show');
    Route::post('groups/posts/delete', 'PostController@destroy');
    Route::post('groups/{id}/posts/create', 'PostController@store');

    Route::post('post/{post_id}/attachment', 'AttachmentsController@store');
    Route::get('post/{post_id}/attachment', 'AttachmentsController@index');

    Route::resource('authenticate', 'AuthenticateController', ['only' => ['index']]);
    Route::post('authenticate', 'AuthenticateController@authenticate');
    Route::get('authenticate/user', 'AuthenticateController@getAuthenticatedUser');

    Route::get('register/{email_id}/verify/{c_code}', 'VerificationController@verify');
    Route::get('register/resend/{email_id}', 'VerificationController@resend_code');
    Route::get('groups/{group_id}/invite/{email_id}', 'VerificationController@invite_user');

});

