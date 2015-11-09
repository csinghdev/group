<?php

namespace App\Http\Controllers;

use App\Group;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;

class VerificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function unverified_users_list()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function add_user(Request $request, $group_id = null)
    {
        //$user_id = JWTAuth::parseToken()->authenticate()->id;

        $admin_id = Group::findOrFail(5)->admin();
        dd($admin_id);
        if( $admin_id )
        {
            return $this->setStatusCode(404)->respondWithError('User is admin');
        }
        return $this->setStatusCode(404)->respondWithError('User is not admin.');
    }

}
