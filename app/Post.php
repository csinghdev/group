<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['title', 'content', 'group_id', 'user_id'];

    /**
     * A post belongs to a single group only.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * A post belongs to a single user only.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Show all comments of a post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Show comments after given comment_id of a post.
     *
     * @param $comment_id
     * @return mixed
     */
    public function newComments($comment_id)
    {
        return $this->hasMany(Comment::class)->where('id','>',$comment_id);
    }

    /**
     * Show attachments of a post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    //public function likes()
    //{
    //    return $this->belongsToMany(User::class, 'like_post');
    //}

}
