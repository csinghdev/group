<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\App;
use Dropbox\Client;
use League\Flysystem\Config;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as Adapter;
//use League\Flysystem\Adapter\Dropbox;
use League\Flysystem\Dropbox\DropboxAdapter as Dropbox;

class CreateAttachmentsTable extends Migration
{

    private $filesystem;

    public function __construct(){

        if(App::environment() === "local")
        {
            // If its on local use the local filesystem
            $this->filesystem = new Filesystem(new Adapter( public_path() ));
        }
        else
        {
            // Use dropbox on other cases,
            $client = new Client(Config::get('dropbox.token'), Config::get('dropbox.appName'));
            $this->filesystem = new Filesystem(new Dropbox($client));

        }

    }


    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->filesystem->createDir('files');

        Schema::create('attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('post_id')->unsigned();
            $table->string('title');
            $table->string('url')->unique();
            $table->timestamps();

            $table->foreign('post_id')
                ->references('id')
                ->on('posts')
                ->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attachments');

        try{
            $this->filesystem->deleteDir('files');
        }catch (\Dropbox\Exception_BadResponse $e){}
    }

}
