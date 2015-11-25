<?php namespace App\Transformers;

use App\Post;
use League\Fractal\TransformerAbstract;

class PostTransformer extends TransformerAbstract {

    protected $availableIncludes = ['comments'];
    // to make it available by default use $defaultIncludes

    public function transform(Post $post) {
        return [
            'id' => (int)$post->id,
            'title' => $post->title,
            'content' => $post->content,
            'group_id' => $post->group_id,
            'user_id' => $post->user_id,
            'created_at' => $post->created_at,
        ];
    }

    public function includeComments(Post $post)
    {
        return $this->collection($post->comments, new CommentTransformer);
    }
}