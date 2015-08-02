<?php

namespace App\Http\Controllers;

use App\Group;
use App\Transformers\UserTransformer;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{

    /**
     * Display all users that belongs to a Group of authenticated user.
     *
     * @param $group_id
     * @return array
     */
    public function index($group_id = null)
    {
        $user_id = JWTAuth::parseToken()->authenticate()->id;

        $group = User::findOrFail($user_id)->groups->find($group_id);

        if ( ! $group )
        {
            return $this->setStatusCode(404)->respondWithError('No users exists in this group.');
        }

        $users = $this->getUsers($group_id);

        return $this->response->collection($users, new UserTransformer);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        if ( ! Input::get('username') or ! Input::get('email') or ! Input::get('password') or ! Input::get('first_name') or ! Input::get('last_name'))
        {
            return $this->respondValidationFailed('Required fields missing.');
        }

        User::create(array(
            'username' => Input::get('username'),
            'email' => Input::get('email'),
            'first_name' => Input::get('first_name'),
            'last_name' => Input::get('last_name'),
            'password' => Hash::make(Input::get('password'))
        ));
//        $users = array(
//            ['username' => 'Max Singh',
//                'first_name' => 'Max',
//                'last_name' => 'Singh',
//                'email' => 'max@gmail.com',
//                'password' => Hash::make('maxmax')],
//
//        );


        return $this->respondCreated('User successfully created.');
    }

    /**
     * @param $group_id
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getUsers($group_id)
    {
        return $group_id ? Group::findOrFail($group_id)->users : User::all();
    }
}
