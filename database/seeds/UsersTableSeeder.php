<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder {

    public function run()
    {
        $users = array(
            ['username' => 'Max Singh',
                'first_name' => 'Max',
                'last_name' => 'Singh',
                'email' => 'max@gmail.com',
                'password' => Hash::make('maxmax')],
            ['username' => 'Chris Sevilleja',
                'first_name' => 'Chris',
                'last_name' => 'Sevilleja',
                'email' => 'max2@gmail.com',
                'password' => Hash::make('maxmax')],
            ['username' => 'Holly Lloyd',
                'first_name' => 'Holly',
                'last_name' => 'Lloyd',
                'email' => 'max3@gmail.com',
                'password' => Hash::make('maxmax'),
                'user_verified' => true ],
            ['username' => 'Adnan Kukic',
                'first_name' => 'Adnan',
                'last_name' => 'Kukic',
                'email' => 'max4@gmail.com',
                'password' => Hash::make('maxmax'),
                'user_verified' => true ],
            ['username' => 'Adnan Sevilleja',
                'first_name' => 'Adnan',
                'last_name' => 'Sevilleja',
                'email' => 'max5@gmail.com',
                'password' => Hash::make('maxmax'),
                'user_verified' => true ],
            ['username' => 'Adnan Chenkie',
                'first_name' => 'Adnan',
                'last_name' => 'Chenkie',
                'email' => 'max6@gmail.com',
                'password' => Hash::make('maxmax'),
                'user_verified' => true ],
        );

        foreach ($users as $user)
        {
            User::create($user);
        }
    }
}