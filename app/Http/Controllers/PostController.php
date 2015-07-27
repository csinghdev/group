<?php

namespace App\Http\Controllers;

use App\Group;
use App\Post;
use App\Transformers\PostTransformer;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($group_id = null)
    {
//        if ( ! Post::find($group_id) )
//        {
//            return $this->setStatusCode(404)->respondWithError('No post exists in this group.');
//        }
//
//        $posts = $this->getPosts($group_id);


        return $this->response->collection(Post::all(), new PostTransformer);
    }

//    public function getPosts($group_id)
//    {
//        return $group_id ? Group::findOrFail($group_id)->posts : Post::all();
//    }

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
}
