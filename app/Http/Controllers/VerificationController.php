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
    public function unverified_users_list()
    {
        //
    }


    public function add_user($group_id = null, $email_id = null)
    {
        $user_id = $this->getAuthUserId();
        $admin_id = Group::findOrFail($group_id)->admin_id;
        if( $user_id != $admin_id )
        {
            return $this->respondWithMessage('User is not admin.');
        }

        $user_list = Verification::whereEmail($email_id)->first();
        if($user_list)
        {
            return $this->respondWithMessage('User already added.');
        }

        Verification::create(array(
                'group_id' => $group_id,
                'email' => $email_id
            ));

        return $this->respondCreated('User successfully added to list.');
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

}
