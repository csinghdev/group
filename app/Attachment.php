<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = ['title', 'post_id', 'url'];

    protected $table = 'attachments';

    public function post()
    {
        return $this->belongsTo('Post', 'post_id');
    }
}
