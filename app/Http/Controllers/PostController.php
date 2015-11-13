<?php

namespace App\Http\Controllers;

use App\Group;
use App\Post;
use App\Transformers\PostTransformer;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Tymon\JWTAuth\Facades\JWTAuth;

class PostController extends Controller
{

//    public function __construct()
//    {
//        $this->middleware('jwt.auth', ['except' => ['index']]);
//    }

    /**
     * Display all posts of a group.
     *
     * @param null $group_id
     * @return array|mixed
     */
    public function index($group_id = null)
    {
        $group = $this->getAuthUserGroup($group_id);

        if ( ! $group )
        {
            return $this->setStatusCode(404)->respondWithError('Group not found.');
        }
        $posts = $this->getPosts($group_id);

//        $likes = [];
//        foreach($posts->lists('id') as $pid) {
//            $likes += DB::table('like_post')->where('post_id', $pid)->lists('post_id','user_id');
//        }
//        dd($likes);
        //dd($posts[1]->user_id);

        //$likes = Post::findOrFail(1)->likes;
        //dd($likes->count());

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
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request, $group_id = null)
    {
        $user_id = $this->getAuthUserId();
        $group = User::findOrFail($user_id)->groups->find($group_id);

        if( ! $group )
        {
            return $this->setStatusCode(404)->respondWithError('Group not found.');
        }

        if ( ! Input::get('title') or ! Input::get('content'))
        {
            return $this->respondValidationFailed('Title or content missing.');
        }

        Post::create(Input::all() + array(
            'user_id' => $user_id,
            'group_id' => $group_id
        ));

        return $this->respondCreated('Post successfully created.');
    }


    /**
     * Show all posts of a user of a group.
     *
     * @param null $group_id
     * @param $user_id
     * @return array|mixed
     */
    public function show($group_id = null)
    {
        $user_id = $this->getAuthUserId();
        $group = User::findOrFail($user_id)->groups->find($group_id);

        if( ! $group )
        {
            return $this->setStatusCode(404)->respondWithError('Group not found.');
        }

        $otherUser = Group::findOrFail($group_id)->users->find($user_id);

        if( ! $otherUser )
        {
            return $this->setStatusCode(404)->respondWithError('User not found.');
        }

        if ( ! Group::find($group_id) or ! User::find($user_id) )
        {
            return $this->setStatusCode(404)->respondWithError('Post not found.');
        }

        $user_posts = $this->getUserPosts($user_id);

        return $this->response->collection($user_posts, new PostTransformer);
    }

    /**
     * Get all posts of a user of a group.
     *
     * @param $user_id
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getUserPosts($user_id)
    {
        return $user_id ? User::findOrFail($user_id)->posts : Post::all();
    }

//    public function post($group_id = null, $post_id)
//    {
//        $user = JWTAuth::parseToken()->authenticate()->id;
//        $group = User::findOrFail($user)->groups->find($group_id);
//
//        if( ! $group )
//        {
//            return $this->setStatusCode(404)->respondWithError('Group not found.');
//        }
//
//        $post = $post_id ? Post::findOrFail($post_id)->posts : Post::all();
//        dd($post);
//
//        return $this->response->collection($post, new PostTransformer);
//    }

    /**
     * Delete a post.
     *
     * @param Request $request
     * @return mixed
     */
    public function destroy(Request $request)
    {
        $group_id = $request->group_id;
        $post_id = $request->post_id;

        $user_id = $this->getAuthUserId();
        $group = User::findOrFail($user_id)->groups->find($group_id);

        if( ! $group )
        {
            return $this->setStatusCode(404)->respondWithError('Group not found.');
        }
        $post = User::findOrFail($user_id)->posts->find($post_id);

        if ( ! $post )
        {
            return $this->setStatusCode(404)->respondWithError('Post not found.');
        }
        if ( $post->delete() )
        {
            return $this->respond("Post successfully deleted");
        }
    }
}
