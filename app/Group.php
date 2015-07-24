<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = ['group_name', 'description'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
