<?php

namespace App\Http\Controllers;

use App\Group;
use App\Transformers\GroupTransformer;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class GroupController extends Controller
{
    /**
     * Remove this, not working
     */
//    public function __construct()
//    {
//        $this->middleware('jwt.auth', ['except' => ['store']]);
//    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
//        $limit = Input::get('limit') ? : 3;
//
//        $groups = Group::paginate($limit);
        $user_id = JWTAuth::parseToken()->authenticate()->id;
        
        $groups = $user_id ? User::findOrFail($user_id)->groups : Group::all();

        if( ! $groups )
        {
            return $this->setStatusCode(404)->respondWithError('Group not found');
        }

        return $this->response->Collection($groups, new GroupTransformer);
    }

    /**
     * Create a new group and store it in DB.
     * group_name is compulsary and description is optional.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        if ( ! Input::get('group_name'))
        {
            return $this->respondValidationFailed('Parameters failed validation for a group');
        }
        Group::create(Input::all());

        return $this->respondCreated('Group successfully created.');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        // temporary authentication
        $this->middleware('jwt.auth');

        $groups = Group::find($id);

        if ( ! $groups )
        {
            return $this->respondNotFound('Group does not exist');
        }

        return $this->response->item($groups, new GroupTransformer);
    }

    /**
     * Update the specified resource in storage.
     *update
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $group = Group::find($id);

        if($group)
        {
            $group->description = $request->description;

            if( ! $group->save() )
            {
                return $this->setStatusCode('500')->respondWithError('Unable to save description.');
            }
        }
        return $this->respond('Successfully updated description.');
    }

}
