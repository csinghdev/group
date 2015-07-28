<?php

namespace App\Http\Controllers;

use App\Group;
use App\Post;
use App\Transformers\PostTransformer;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PostController extends Controller
{

    /**
     * Display all posts of a group.
     *
     * @param null $group_id
     * @return array|mixed
     */
    public function index($group_id = null)
    {
        if ( ! Group::find($group_id) )
        {
            return $this->setStatusCode(404)->respondWithError('No such group exists.');
        }
        $posts = $this->getPosts($group_id);

        return $this->response->collection($posts, new PostTransformer);
    }

    /**
     * Get all posts of a group.
     *
     * @param $group_id
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getPosts($group_id)
    {
        return $group_id ? Group::findOrFail($group_id)->posts : Post::all();
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
        //
    }


    /**
     * Display posts of user of a group.
     * @param null $group_id
     * @param $user
     * @return array
     */
    public function show($group_id = null, $user)
    {
        // not recommended way to use relationship
        // show only those posts which are created by user of this group only
        $user_posts = Post::where("user_id", "=", $user )->get();

        return $this->response->collection($user_posts, new PostTransformer);
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
}
