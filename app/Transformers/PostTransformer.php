<?php namespace App\Transformers;

use App\Post;
use League\Fractal\TransformerAbstract;

class PostTransformer extends TransformerAbstract {

    protected $defaultIncludes = ['comments'];

    public function transform(Post $post) {
        return [
            'id' => (int)$post->id,
            'title' => $post->title,
            'content' => $post->content,
            'likes_count' => $post->likes_count,
            'group_id' => $post->group_id,
            'user_id' => $post->user_id,
        ];
    }

    public function includeComments(Post $post)
    {
        return $this->collection($post->comments, new CommentTransformer);
    }
}