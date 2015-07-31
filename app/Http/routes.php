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

    Route::resource('groups', 'GroupController', ['only' => ['index', 'show', 'store', 'update']]);

    Route::get('posts/{id}/comments', 'CommentController@index');

    Route::get('groups/{id}/posts', 'PostController@index');
    Route::get('groups/{id}/posts/{user}', 'PostController@show');
    Route::post('groups/posts/delete', 'PostController@destroy');

    Route::resource('authenticate', 'AuthenticateController', ['only' => ['index']]);
    Route::post('authenticate', 'AuthenticateController@authenticate');
    Route::get('authenticate/user', 'AuthenticateController@getAuthenticatedUser');
});

