<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $table = 'attachments';

    public function post()
    {
        return $this->belongsTo('Post', 'post_id');
    }
}
