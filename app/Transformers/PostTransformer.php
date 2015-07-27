<?php namespace App\Transformers;

use App\Post;
use League\Fractal\TransformerAbstract;

class PostTransformer extends TransformerAbstract {

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
}