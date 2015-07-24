<?php

use App\Group;
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
        DB::table('group_user')->truncate();

       // DB::statement("SET foreign_key_checks = 1");

        Model::unguard();

        $this->call(GroupsTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(GroupUserTableSeeder::class);


        Model::reguard();
    }
}
