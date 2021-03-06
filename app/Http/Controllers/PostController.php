<?php

namespace App\Http\Controllers;

use App\Group;
use App\Jobs\SendNotification;
use App\Post;
use App\Transformers\PostTransformer;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\App;
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
    public function index($group_id = null, $post_id = null)
    {
        $group = $this->getAuthUserGroup($group_id);
        if ( ! $group )
        {
            return $this->setStatusCode(404)->respondWithError('Group not found.');
        }

        if($post_id)
        {
            $posts = $this->getNewPosts($group_id, $post_id);
        }
        else
        {
            $posts = $this->getPosts($group_id);
        }

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

    public function getNewPosts($group_id, $post_id)
    {
        return $group_id ? Group::findOrFail($group_id)->newPosts($post_id)->get() : Post::all();
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
        $user = User::findOrFail($user_id);
        $group = $user->groups->find($group_id);

        $username = $user->username;
        $title = Input::get('title');
        $content = Input::get('content');

        if( ! $group )
        {
            return $this->setStatusCode(404)->respondWithError('Group not found.');
        }

        if ( ! $title or ! $content)
        {
            return $this->respondValidationFailed('Title or content missing.');
        }

        Post::create(Input::all() + array(
            'user_id' => $user_id,
            'group_id' => $group_id
        ));

        if(App::environment() != "local") {
            $users_ids = Group::find($group_id)->users->lists('id');
            $ios_tokens = array();
            $android_tokens = array();
            foreach ($users_ids as $user_id) {
                $token_info = User::findOrFail($user_id)->notificationToken;
                if ($token_info) {
                    if ($token_info->ios === "1") {
                        array_push($ios_tokens, $token_info->token);
                    } else {
                        array_push($android_tokens, $token_info->token);
                    }
                }
            }
            $message = array('group' => $group->group_name,
                'user' => $username,
                'title' => $title);
            $type = 'post';
            $job = (new SendNotification($ios_tokens, $android_tokens, $message, $type))->delay(60);
            $this->dispatch($job);
        }

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
