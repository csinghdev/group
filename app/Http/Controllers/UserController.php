<?php

namespace App\Http\Controllers;

use App\Group;
use App\Transformers\UserTransformer;
use App\User;
use App\Verification;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;

use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{

    private $user_image_path = '/user_image';
    /**
     * Display all members that belongs to a Group of authenticated user.
     *
     * @param $group_id
     * @return array
     */
    public function index($group_id = null)
    {
        $group = $this->getAuthUserGroup($group_id);

        if ( ! $group )
        {
            return $this->respondGroupValidationFailed();
        }

        $users = $this->getUsers($group_id);

        return $this->response->collection($users, new UserTransformer);
    }

    /**
     * Store as well as update user image.
     *
     * @param Request $request
     * @return mixed
     */
    public function storeImage(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $image = $request->file('image');

        if(!$image)
        {
            return $this->respondNotFound("Image not found");
        }

        if ($user)
        {

            if( $user->image_url == null)
            {
                $image_url = $this->saveImage($image, $this->user_image_path);

                $user->image_url = $image_url;
            }
            else
            {
                $image_url = $this->updateImage($image, $this->user_image_path, $user->image_url);

                $user->image_url = $image_url;
            }

            if( ! $user->save() )
            {
                return $this->setStatusCode('500')->respondWithError('Unable to save image.');
            }
        }

        return $this->respondCreated("User image saved successfully.");
    }


    /**
     * Create a new user and send confirmation mail or add invited user with unique code.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        if ( ! Input::get('username') or ! Input::get('email') or ! Input::get('password') or ! Input::get('first_name') or ! Input::get('last_name'))
        {
            return $this->respondValidationFailed('Required fields missing.');
        }
        $verified = 0;
        $confirmation_code = str_random(8);
        $group = null;
        if ( Input::get('unique_code') )
        {
            $unique_code = Input::get('unique_code');
            $email = Input::get('email');
            $invited_user = Verification::whereEmail($email)->first();
            if ($invited_user)
            {
                $group_id = $invited_user->group_id;
                $group = Group::findOrFail($group_id);
                if ($group)
                {
                    if ( $unique_code === $group->unique_code)
                    {
                        $invited_user->delete();
                        $confirmation_code = null;
                        $verified = 1;
                    }
                    else
                    {
                        return $this->respondWithMessage("Invalid code.");
                    }
                }
            }
            else
            {
                return $this->respondNotFound("Uninvited user.");
            }
        }

        User::create(array(
            'username' => Input::get('username'),
            'email' => Input::get('email'),
            'first_name' => Input::get('first_name'),
            'last_name' => Input::get('last_name'),
            'password' => Hash::make(Input::get('password')),
            'confirmation_code' => $confirmation_code,
            'user_verified' => $verified
        ));
        //$group = Group::findOrFail(5);
        if ($group)
        {
            $user = User::whereEmail(Input::get('email'))->first();
            $user_id = $user->id;
            $group->users()->attach($user_id);
        }
        else
        {
            Mail::queue('emails.verify', ['confirmation_code' => $confirmation_code, 'username' => Input::get('username'), 'email' => Input::get('email')], function($message) {
                $message->to(Input::get('email'), Input::get('username'))
                    ->subject('Verify your email address');
            });
        }

        return $this->respondCreated('User successfully created.');
    }

    /**
     * Get users of provided groupId.
     *
     * @param $group_id
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getUsers($group_id)
    {
        return $group_id ? Group::findOrFail($group_id)->users : User::all();
    }
}
