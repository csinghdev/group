<?php

namespace App\Http\Controllers;

use App\Group;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
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

    public function resend_code($email_id = null)
    {
        $user = User::whereEmail($email_id)->first();
        if (!$user)
        {
            return $this->respondNotFound("User not found.");
        }

        if ($user->user_verified)
        {
            return $this->respondWithMessage("User already verified.");
        }

        $user_details = ['confirmation_code' => $user->confirmation_code,
                         'username' => $user->username,
                         'email' => $user->email];

        Mail::queue('emails.verify', $user_details, function ($message) use ($user_details){
            $message->to($user_details['email'], $user_details['username'])
                ->subject('Verify your email address');
        });

        return $this->respondWithMessage("Mail successfully sent.");
    }

    public function verify($email_id = null, $c_code = null)
    {
        if (!$email_id or !$c_code)
        {
            return $this->respondNotFound("Missing email id or Confirmation Code.");
        }

        //$user = User::whereConfirmationCode($c_code)->first();
        $user = User::whereEmail($email_id)->first();
        if (!$user)
        {
            return $this->respondNotFound("User not found.");
        }

        $code = $user->confirmation_code;

        if ($code != $c_code)
        {
            return $this->respondWithError("Wrong confirmation code.");
        }

        $user->user_verified = 1;
        $user->confirmation_code = null;
        $user->save();

        return $this->respondCreated("User successfully verified.");
    }

}
