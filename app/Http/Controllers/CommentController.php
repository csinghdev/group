<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Post;
use App\Transformers\CommentTransformer;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CommentController extends Controller
{

    /**
     * Comments of a post.
     *
     * @param null $post_id
     * @return array|mixed
     */
    public function index( $post_id = null )
    {
        if ( ! Post::find($post_id) )
        {
            return $this->setStatusCode(404)->respondWithError('No such post exists.');
        }
        $comments = $this->getComments($post_id);

        return $this->response->collection($comments, new CommentTransformer);
    }

    /**
     * Get comments of a post.
     *
     * @param $post_id
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getComments($post_id)
    {
        return $post_id ? Post::findOrFail($post_id)->comments : Comment::all();
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

}
