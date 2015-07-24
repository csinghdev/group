<?php

namespace App\Http\Controllers;

use App\Group;
use App\Transformers\UserTransformer;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    /**
     * Display all users that belongs to a Group.
     *
     * @param $group_id
     * @return array
     */
    public function index($group_id = null)
    {
        if ( ! Group::find($group_id) )
        {
            return $this->setStatusCode(404)->respondWithError('No users exists in this group.');
        }

        $users = $this->getUsers($group_id);

        return $this->response->collection($users, new UserTransformer);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $users = array(
            ['username' => 'Max Singh',
                'first_name' => 'Max',
                'last_name' => 'Singh',
                'email' => 'max@gmail.com',
                'password' => Hash::make('maxmax')],
            ['username' => 'Chris Sevilleja',
                'first_name' => 'Chris',
                'last_name' => 'Sevilleja',
                'email' => 'max2@gmail.com'
                , 'password' => Hash::make('maxmax')],
            ['username' => 'Holly Lloyd',
                'first_name' => 'Holly',
                'last_name' => 'Lloyd',
                'email' => 'max3@gmail.com'
                , 'password' => Hash::make('maxmax')],
            ['username' => 'Adnan Kukic',
                'first_name' => 'Adnan',
                'last_name' => 'Kukic',
                'email' => 'max4@gmail.com'
                , 'password' => Hash::make('maxmax')],
            ['username' => 'Adnan Sevilleja',
                'first_name' => 'Adnan',
                'last_name' => 'Sevilleja',
                'email' => 'max5@gmail.com'
                , 'password' => Hash::make('maxmax')],
            ['username' => 'Adnan Chenkie',
                'first_name' => 'Adnan',
                'last_name' => 'Chenkie',
                'email' => 'max6@gmail.com'
                , 'password' => Hash::make('maxmax')],
        );

        foreach ($users as $user)
        {
            User::create($user);
        }

        return User::all();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * @param $user_id
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getUsers($group_id)
    {
        return $group_id ? Group::findOrFail($group_id)->users : User::all();
    }
}
