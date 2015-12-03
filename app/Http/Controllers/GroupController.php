<?php

namespace App\Http\Controllers;

use App\Group;
use App\Transformers\GroupTransformer;
use App\User;
use App\Verification;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class GroupController extends Controller
{

    private $group_image_path = '/group_image';
    /**
     * Remove this, not working
     */
//    public function __construct()
//    {
//        $this->middleware('jwt.auth', ['except' => ['store']]);
//    }

    /**
     * Display group details to authenticated user.
     *
     * @return Response
     */
    public function index()
    {
//        $limit = Input::get('limit') ? : 3;
//
//        $groups = Group::paginate($limit);
        $user_id = $this->getAuthUserId();

        $groups = $user_id ? User::findOrFail($user_id)->groups : Group::all();

        if( ! $groups )
        {
            return $this->respondGroupValidationFailed();
        }

        return $this->response->Collection($groups, new GroupTransformer);
    }

    /**
     * Create a new group and store it in DB.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $user_id = $this->getAuthUserId();

        $user = User::findOrFail($user_id);

        if ($user->user_verified == 0)
        {
            return $this->respondWithMessage("Unverified User.");
        }

        if ( ! Input::get('group_name'))
        {
            return $this->respondValidationFailed('Parameters failed validation for a group');
        }

        $group = Group::create(array(
            'group_name' => Input::get('group_name'),
            'description' => Input::get('description'),
            'unique_code' => str_random(8),
            'admin_id' => $user_id
        ));

        $group->users()->attach($user_id);

        return $this->respondCreated('Group successfully created.');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
//    public function show($id)
//    {
//        // temporary authentication
//        $this->middleware('jwt.auth');
//
//        $groups = Group::find($id);
//
//        if ( ! $groups )
//        {
//            return $this->respondNotFound('Group does not exist');
//        }
//
//        return $this->response->item($groups, new GroupTransformer);
//    }

    public function storeImage(Request $request, $id)
    {
        $group = $this->getAuthUserGroup($id);
        $image = $request->file('image');

        if(!$image)
        {
            return $this->respondNotFound("Image not found");
        }

        if($group)
        {
            if ($group and $image)
            {

                if( $group->group_image_url == null)
                {
                    $image_url = $this->saveImage($image, $this->group_image_path);

                    $group->group_image_url = $image_url;
                }
                else
                {
                    $image_url = $this->updateImage($image, $this->group_image_path, $group->group_image_url);

                    $group->group_image_url = $image_url;
                }

                if( ! $group->save() )
                {
                    return $this->setStatusCode('500')->respondWithError('Unable to save image.');
                }
            }
        }

        return $this->respondCreated("Group image saved successfully.");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $group = $this->getAuthUserGroup($id);

        //$group = Group::find($id);

        if($group)
        {
            $group->description = $request->description;

            if( ! $group->save() )
            {
                return $this->setStatusCode('500')->respondWithError('Unable to save description.');
            }
        }
        else
        {
            return $this->respondGroupValidationFailed();
        }
        return $this->respond('Successfully updated description.');
    }

    /**
     * Add authenticated invited user with valid unique code to group.
     *
     * @param null $unique_code
     * @return mixed
     */
    public function joinGroup($unique_code = null)
    {
        $user_id = $this->getAuthUserId();

        if(!$unique_code)
        {
            return $this->respondValidationFailed("Missing unique code");
        }

        $email_id = User::findOrFail($user_id)->email;

        $invited_user = Verification::whereEmail($email_id)->first();

        if( ! $invited_user )
        {
            return $this->respondValidationFailed("Uninvited user");
        }

        $group = Group::findOrFail($invited_user->group_id);

        if ( $unique_code === $group->unique_code )
        {
            $group->users()->attach($user_id);
        }
        else
        {
            return $this->respondValidationFailed("Wrong Code");
        }

        return $this->respondCreated("User successfully added to group");
    }

}
