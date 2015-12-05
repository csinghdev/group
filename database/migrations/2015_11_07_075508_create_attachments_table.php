<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\App;
use Dropbox\Client;
use Illuminate\Support\Facades\Config;
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
            $this->filesystem = new Filesystem(new Adapter( public_path() . '/files/'));
        }
        else
        {
            // Use dropbox on other cases,
            $client = new Client(Config::get('dropbox.token'), Config::get('dropbox.appName'));
            $this->filesystem = new Filesystem(new Dropbox($client, '/files/'));

        }

    }


    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->filesystem->createDir('attachments');

        Schema::create('attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('post_id')->unsigned();
            $table->string('title')->nullable();
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
            $this->filesystem->deleteDir('attachments');
        }catch (\Dropbox\Exception_BadResponse $e){}
    }

}
