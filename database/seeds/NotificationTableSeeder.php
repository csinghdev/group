<?php

use App\Notification;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class NotificationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        foreach(range(1, 3) as $index)
        {
            Notification::create([
                'user_id' => $index,
                'ios' => 1,
                'token' => $faker->sentence(20)
            ]);
        }
    }
}
