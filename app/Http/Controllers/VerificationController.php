<?php

namespace App\Http\Controllers;

use App\Group;
use App\User;
use App\Verification;
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
    public function accept_invitation()
    {
        //
    }


    public function invite_user($group_id = null, $email_id = null)
    {
        $user_id = $this->getAuthUserId();
        $group = Group::findOrFail($group_id);
        $admin_id = $group->admin_id;
        if( $user_id != $admin_id )
        {
            return $this->respondWithMessage('Logged in user is not admin.');
        }
        $user = User::findOrFail($user_id);

        $user_list = Verification::whereEmail($email_id)->first();

        $user_details = ['confirmation_code' => $group->unique_code,
            'username' => $user->username,
            'email' => $email_id,
            'group_name' => $group->group_name];

        if($user_list)
        {
            $this->send_invitation($user_details);
            return $this->respondWithMessage('User already invited.');
        }

        Verification::create(array(
                'group_id' => $group_id,
                'email' => $email_id
            ));

        $this->send_invitation($user_details);

        return $this->respondCreated('User successfully invited.');
    }

    /**
     * Resend Confirmation Code to Registered Users.
     *
     * @param null $email_id
     * @return mixed
     */
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

    /**
     * Verify User with a valid Confirmation Code.
     *
     * @param null $email_id
     * @param null $c_code
     * @return mixed
     */
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


    /**
     * @param $user_details
     */
    public function send_invitation($user_details)
    {
        Mail::queue('emails.addUser', $user_details, function ($message) use ($user_details) {
            $message->to($user_details['email'], $user_details['username'])
                ->subject('Verify your email address');
        });
    }

}
