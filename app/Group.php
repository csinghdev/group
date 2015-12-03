<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = ['group_name', 'description', 'group_image_url', 'unique_code', 'admin_id'];

    /**
     * A Group belongs to many users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * A group can have many posts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Show posts after given post_id.
     *
     * @param $post_id
     * @return mixed
     */
    public function newPosts($post_id)
    {
        return $this->hasMany(Post::class)->where('id','>',$post_id);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function admin()
    {
        return $this->hasOne(User::class);
    }
}
