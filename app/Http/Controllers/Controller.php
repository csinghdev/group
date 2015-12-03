<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Sorskod\Larasponse\Larasponse;
use Tymon\JWTAuth\Facades\JWTAuth;

use Dropbox\Client;
use Illuminate\Support\Facades\Config;
use League\Flysystem\Filesystem;
use League\Flysystem\Dropbox\DropboxAdapter as Dropbox;


abstract class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests;

    protected $response;

    protected $statusCode = 200;

    private $filesystem;

    function __construct(Larasponse $response)
    {
        $this->response = $response;
        if(Input::has('includes'))
        {
            $this->response->parseIncludes(Input::get('includes'));
        }
    }

    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }


    /**
     * @param $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * @param string $message
     * @return mixed
     */
    public function respondNotFound($message = 'Not Found')
    {
        $this->setStatusCode(404);
        return $this->respondWithError($message);
    }

    /**
     * @param $data
     * @param array $headers
     * @return mixed
     */
    public function respond($data, $headers = [])
    {
        return Response::json($data, $this->getStatusCode(), $headers);
    }

    /**
     * @param $message
     * @return mixed
     */
    public function respondWithError($message)
    {
        return $this->respond([
            'error' => [
                'message' => $message,
                'status_code' => $this->getStatusCode()
            ]
        ]);
    }


    /**
     * @param $message
     * @return mixed
     */
    public function respondCreated($message)
    {
        return $this->setStatusCode(201)->respond([
            'message' => $message
        ]);
    }

    /**
     * @param $message
     * @return mixed
     */
    public function respondWithMessage($message)
    {
        return $this->setStatusCode(200)->respond([
            'message' => $message
        ]);
    }


    /**
     * @param $message
     * @return mixed
     */
    public function respondValidationFailed($message)
    {
        return $this->setStatusCode(422)->respondWithError($message);
    }

    /**
     * Get Authenticated User Id.
     *
     * @return mixed
     */
    public function getAuthUserId()
    {
        return JWTAuth::parseToken()->authenticate()->id;
    }

    /**
     * Get groups of Authenticated User.
     *
     * @param $group_id
     * @return mixed
     */
    public function getAuthUserGroup($group_id)
    {
        $user_id = $this->getAuthUserId();
        return User::findOrFail($user_id)->groups->find($group_id);
    }

    /**
     * Error message if User does not belong to Group.
     *
     * @return mixed
     */
    public function respondGroupValidationFailed()
    {
        return $this->setStatusCode(404)->respondWithError('Group Not Found.');
    }


    /**
     * Save file to dropbox.
     *
     * @param $file
     * @param $path
     * @return string
     */
    public function saveFile($file, $path)
    {
        $client = new Client(Config::get('dropbox.token'), Config::get('dropbox.appName'));
        $this->filesystem = new Filesystem(new Dropbox($client, $path));

        $url = str_random(20) . "." . $file->getClientOriginalExtension();

        try {
            $this->filesystem->write($url, file_get_contents($file));
            return $url;
        } catch (\Dropbox\Exception $e) {
            echo $e->getMessage();
        }

        return $url;
    }


    /**
     * Delete previous image and store new image.
     *
     * @param $image
     * @param $path
     * @param $old_image_url
     * @return string
     */
    public function updateImage($image, $path, $old_image_url)
    {
        $client = new Client(Config::get('dropbox.token'), Config::get('dropbox.appName'));
        $this->filesystem = new Filesystem(new Dropbox($client, $path));

        $url = str_random(20) . "." . $image->getClientOriginalExtension();

        if ($this->filesystem->has($old_image_url))
        {
            try {
                $this->filesystem->delete($old_image_url);
            } catch (\Dropbox\Exception $e) {
                echo $e->getMessage();
            }
        }

        try {
            $this->filesystem->write($url, file_get_contents($image));
            return $url;
        } catch (\Dropbox\Exception $e) {
            echo $e->getMessage();
        }

        return $url;
    }

}
