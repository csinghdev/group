<?php

namespace App\Http\Controllers;

use App\Group;
use App\Transformers\GroupTransformer;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;

class GroupController extends Controller
{
    /**
     * Authenticate user except for store method.
     */
    public function __construct()
    {
        $this->middleware('jwt.auth', ['except' => ['store']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $groups = Group::all();

        return $this->response->collection($groups, new GroupTransformer);
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

}
