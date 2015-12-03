<?php

namespace App\Http\Controllers;

use App\Attachment;
use App\Group;
use App\Post;
use App\Transformers\AttachmentTransformer;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;

use Illuminate\Support\Facades\Input;


class AttachmentsController extends Controller
{

    private $attachment_path = '/attachments';

    /**
     * Display attachments of a post of a group whose member is authenticated user.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($group_id = null, $post_id = null)
    {
        $user_id = $this->getAuthUserId();

        $group_member = Group::findOrFail($group_id)->users->find($user_id);

        if ( ! $group_member )
        {
            return $this->setStatusCode(404)->respondWithError('User does not belong to the group.');
        }

        $post = Group::findOrFail($group_id)->posts->find($post_id);

        if ( ! $post )
        {
            return $this->setStatusCode(404)->respondWithError('Post not found.');
        }

        $attachment = $post_id ? Post::findOrFail($post_id)->attachments : Attachment::all();

        if( ! $attachment )
        {
            return $this->setStatusCode(404)->respondWithError('Attachment not found.');
        }

        return $this->response->collection($attachment, new AttachmentTransformer);
    }


    /**
     * add an attachment to a post and store it in dropbox
     *
     * @param Request $request
     * @param null $group_id
     * @param null $post_id
     * @return mixed
     */
    public function store(Request $request, $group_id = null, $post_id = null)
    {
        $user_id = $this->getAuthUserId();

        $group_member = Group::findOrFail($group_id)->users->find($user_id);

        if ( ! $group_member )
        {
            return $this->setStatusCode(404)->respondWithError('User does not belong to the group.');
        }

        $group_post = Group::findOrFail($group_id)->posts->find($post_id);

        $post = User::findOrFail($user_id)->posts->find($post_id);

        if ( !$group_post and ! $post )
        {
            return $this->setStatusCode(404)->respondWithError('Post not found.');
        }

        $title = Input::get('title');

        $file = $request->file('file');

        if (!$file)
        {
            return $this->respondNotFound("Attachment not found");
        }

        $url = $this->saveFile($file, $this->attachment_path);

        if(!$url)
        {
            return $this->setStatusCode('500')->respondWithError('Unable to save attachment.');
        }

        Attachment::create(array(
            'url' => $url,
            'post_id' => $post_id,
            'title' => $title
        ));

        return $this->respondCreated('Attachment successfully uploaded.');
    }

}
