<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Group;
use App\Post;
use App\Transformers\CommentTransformer;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Tymon\JWTAuth\Facades\JWTAuth;

class CommentController extends Controller
{

    /**
     * Comments of a post.
     *
     * @param null $post_id
     * @return array|mixed
     */
    public function index( $group_id = null, $post_id = null )
    {
        $group = $this->getAuthUserGroup($group_id)->id;
        if( !$group )
        {
            return $this->setStatusCode(404)->respondWithError('Group not found.');
        }

        $post = Group::findOrFail($group_id)->posts->find($post_id);

        if ( ! $post )
        {
            return $this->setStatusCode(404)->respondWithError('Post not found.');
        }
        $comments = $post->comments;

        return $this->response->collection($comments, new CommentTransformer);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request, $group_id = null, $post_id = null)
    {
        $user_id = $this->getAuthUserId();

        $group_user = Group::findOrFail($group_id)->users->find($user_id);

        if( !$group_user )
        {
            return $this->setStatusCode(404)->respondWithError('Group not found.');
        }

        $post = Group::findOrFail($group_id)->posts->find($post_id);

        if( ! $post )
        {
            return $this->setStatusCode(404)->respondWithError('Post not found.');
        }

        if ( ! Input::get('comment'))
        {
            return $this->respondValidationFailed('Content missing.');
        }

        Comment::create(Input::all() + array(
                'user_id' => $user_id,
                'post_id' => $post_id
            ));

        return $this->respondCreated('Comment successfully posted.');
    }

}
