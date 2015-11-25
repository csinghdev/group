<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = ['user_id', 'ios', 'token'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
