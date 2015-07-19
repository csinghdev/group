<?php

use App\Group;
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

        Model::unguard();

        $this->call(GroupsTableSeeder::class);

        Model::reguard();
    }
}
