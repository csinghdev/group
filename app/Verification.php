<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    protected $table = 'unverified_users';

    protected $fillable = ['group_id', 'email', 'message', 'verification_code'];

}
