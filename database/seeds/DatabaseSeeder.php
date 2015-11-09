<?php

use App\Comment;
use App\Group;
use App\Post;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement("SET foreign_key_checks = 0");

        Group::truncate();
        User::truncate();
        Post::truncate();
        Comment::truncate();

        DB::table('group_user')->truncate();
        DB::table('like_post')->truncate();

       // DB::statement("SET foreign_key_checks = 1");

        Model::unguard();

        $this->call(GroupsTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(GroupUserTableSeeder::class);
        $this->call(PostTableSeeder::class);
        $this->call(CommentsTableSeeder::class);
        $this->call(LikePostTableSeeder::class);
        //$this->call(AttachmentTableSeeder::class);
        $this->call(AdminsTableSeeder::class);
        $this->call(UnverifiedUsersTableSeeder::class);


        Model::reguard();
    }
}
