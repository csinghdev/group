<?php

use App\Group;
use App\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class GroupUserTableSeeder extends Seeder {

    public function run()
    {
        $faker = Faker::create();

        $user_id = User::lists('id')->all();
        $group_id = Group::lists('id')->all();

        foreach(range(1, 30) as $index)
        {
            DB::table('group_user')->insert([
                'group_id' => $faker->randomElement($group_id),
                'user_id' => $faker->randomElement($user_id)

            ]);
        }
    }
}