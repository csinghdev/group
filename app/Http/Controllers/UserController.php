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

use Dropbox\Client;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use League\Flysystem\Filesystem;
use League\Flysystem\Dropbox\DropboxAdapter as Dropbox;

class UserController extends Controller
{
    private $filesystem;
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

        $image = $request->file('image');
        $image_url = null;
        if ($image)
        {
            $image_url = $this->saveImage($image);
        }

        User::create(array(
            'username' => Input::get('username'),
            'email' => Input::get('email'),
            'first_name' => Input::get('first_name'),
            'last_name' => Input::get('last_name'),
            'password' => Hash::make(Input::get('password')),
            'image_url' => $image_url,
            'confirmation_code' => $confirmation_code,
            'user_verified' => $verified
        ));
        $group = Group::findOrFail(5);
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

    /**
     * Save user image provided by user to dropbox.
     *
     * @param $image
     * @return string
     */
    public function saveImage($image)
    {
        $client = new Client(Config::get('dropbox.token'), Config::get('dropbox.appName'));
        $this->filesystem = new Filesystem(new Dropbox($client, '/user_image'));

        $url = str_random(20) . "." . $image->getClientOriginalExtension();

        try {
            $this->filesystem->write($url, file_get_contents($image));
            return $url;
        } catch (\Dropbox\Exception $e) {
            echo $e->getMessage();
        }

        return $url;
    }
}
