<?php

use App\Group;
use App\Verification;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class UnverifiedUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $group_id = Group::lists('id')->all();

        $faker = Faker::create();

        foreach(range(1, 50) as $index)
        {
            Verification::create([
                'group_id' => $faker->randomElement($group_id),
                'email' => $faker->email,
                'message' => $faker->sentence(10),
                'verification_code' => $faker->word,
            ]);
        }
    }
}
