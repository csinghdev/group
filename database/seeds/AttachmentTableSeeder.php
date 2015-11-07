<?php

use App\Attachment;
use Illuminate\Database\Seeder;
use Dropbox\Client;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as Adapter;
//use League\Flysystem\Adapter\Dropbox;
use League\Flysystem\Dropbox\DropboxAdapter as Dropbox;

class AttachmentTableSeeder extends Seeder
{
    private $filesystem;

    public function __construct(){
        if(App::environment() === "local"){
            $this->filesystem = new Filesystem(new Adapter( public_path() . '/files/'));
        }
        else
        {
            $client = new Client(Config::get('dropbox.token'), Config::get('dropbox.appName'));
            $this->filesystem = new Filesystem(new Dropbox($client, '/files'));
        }

    }

    public function run()
    {

        Eloquent::unguard();

        DB::table('attachments')->delete();

        $faker = Faker\Factory::create();

        for($i = 0; $i < 10; $i++){

            $file = file_get_contents('http://lorempixel.com/640/400/');

            $url = $faker->lexify($string = '???????????????????');
            try{
                $this->filesystem->write($url, $file);
            }catch (\Dropbox\Exception $e){
                echo $e->getMessage();
            }


            Attachment::create(array(
                'url' => $url,
                'post_id' => (int) rand(1, 4),
                'title' => $faker->sentence(5)
            ));
        }

    }

}
