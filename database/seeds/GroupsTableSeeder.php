<?php

use App\Group;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class GroupsTableSeeder extends Seeder {

    public function run()
    {
        $faker = Faker::create();

        foreach(range(1, 10) as $index)
        {
            Group::create([

                'group_name' => $faker->sentence(3),
                'description' => $faker->sentence(10)
            ]);
        }
    }
}