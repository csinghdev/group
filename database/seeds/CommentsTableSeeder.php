<?php

use App\Comment;
use App\Post;
use App\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class CommentsTableSeeder extends Seeder {

    public function run()
    {
        $faker = Faker::create();

        $user_id = User::lists('id')->all();
        $post_id = Post::lists('id')->all();

        foreach(range(1, 50) as $index)
        {
            Comment::create([
                'comment' => $faker->sentence(10),
                'post_id' => $faker->randomElement($post_id),
                'user_id' => $faker->randomElement($user_id)
            ]);
        }
    }
}