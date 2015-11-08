<?php

namespace App\Http\Controllers;

use App\Attachment;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Dropbox\Client;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as Adapter;
use League\Flysystem\Dropbox\DropboxAdapter as Dropbox;
use Tymon\JWTAuth\Facades\JWTAuth;

class AttachmentsController extends Controller
{
    private $filesystem;

    public function __construct(){
        if(App::environment() === "local"){
            //$this->filesystem = new Filesystem(new Adapter( public_path() . '/files/'));
            $client = new Client(Config::get('dropbox.token'), Config::get('dropbox.appName'));
            $this->filesystem = new Filesystem(new Dropbox($client, '/files'));

        }
        else
        {
            $client = new Client(Config::get('dropbox.token'), Config::get('dropbox.appName'));
            $this->filesystem = new Filesystem(new Dropbox($client, '/files'));
        }

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $post_id = null)
    {
        $user_id = JWTAuth::parseToken()->authenticate()->id;

        $post = User::findOrFail($user_id)->posts->find($post_id);

        if ( ! $post )
        {
            return $this->setStatusCode(404)->respondWithError('Post not found.');
        }

        $title = Input::get('title');

        $file = $request->file('file');

        $url = str_random(30) . "." . $file->getClientOriginalExtension();

        try{
            $this->filesystem->write($url, file_get_contents($file));
        }catch (\Dropbox\Exception $e){
            echo $e->getMessage();
        }

        Attachment::create(array(
            'url' => $url,
            'post_id' => $post_id,
            'title' => $title
        ));

        return $this->respondCreated('Attachment successfully uploaded.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
