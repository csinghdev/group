<?php

use App\Group;
use App\Post;
use App\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class PostTableSeeder extends Seeder {

    public function run()
    {
        $faker = Faker::create();

        $user_id = User::lists('id')->all();
        $group_id = Group::lists('id')->all();

        foreach(range(1, 20) as $index)
        {
            Post::create([
                'title' => $faker->sentence(3),
                'content' => $faker->sentence(10),
                'likes_count' => $faker->numberBetween(0, 10),
                'group_id' => $faker->randomElement($group_id),
                'user_id' => $faker->randomElement($user_id)
            ]);
        }
    }
}