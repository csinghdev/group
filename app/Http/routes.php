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

Route::group(['prefix' => 'api/v1'], function() {
    Route::get('group/{group_id}/users', 'UserController@index');
    Route::resource('user', 'UserController', ['only' => ['store']]);

    Route::resource('groups', 'GroupController', ['only' => ['index', 'store', 'update']]);
    Route::get('group/{unique_code}', 'GroupController@joinGroup');

    Route::get('post/{post_id}/comments', 'CommentController@index');
    Route::post('post/{post_id}/comment', 'CommentController@store');

    // Use includes=comments in post routes to include comments also
    Route::get('group/{group_id}/posts', 'PostController@index');
    Route::get('group/{group_id}/posts/{post_id}', 'PostController@index');
    Route::get('group/{group_id}/user/posts', 'PostController@show');
    Route::post('post/delete', 'PostController@destroy');
    Route::post('group/{group_id}/post/create', 'PostController@store');

    Route::post('post/{post_id}/attachment', 'AttachmentsController@store');
    Route::get('group/{group_id}/post/{post_id}/attachment', 'AttachmentsController@index');

    Route::resource('authenticate', 'AuthenticateController', ['only' => ['index']]);
    Route::post('authenticate', 'AuthenticateController@authenticate');
    Route::get('authenticate/user', 'AuthenticateController@getAuthenticatedUser');

    Route::get('register/{email_id}/verify/{c_code}', 'VerificationController@verify');
    Route::get('register/resend/{email_id}', 'VerificationController@resendCode');
    Route::get('group/{group_id}/invite/{email_id}', 'VerificationController@inviteUser');

    Route::resource('notification', 'NotificationController', ['only' => ['store','update']]);

});

