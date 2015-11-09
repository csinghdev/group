<?php namespace App\Transformers;

use App\Comment;
use League\Fractal\TransformerAbstract;

class CommentTransformer extends TransformerAbstract {

    public function transform(Comment $comment) {

        return [
            'id' => (int)$comment->id,
            'user_id' => $comment->user_id,
            'comment' => $comment->comment,
            'created_at' => $comment->created_at,
        ];
    }
}