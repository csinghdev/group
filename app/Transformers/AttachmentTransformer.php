<?php namespace App\Transformers;

use App\Attachment;
use League\Fractal\TransformerAbstract;

class AttachmentTransformer extends TransformerAbstract {

    public function transform(Attachment $attachment) {
        return [
            'id' => (int)$attachment->id,
            'url' => $attachment->url,
            'title' => $attachment->title
        ];
    }
}