<?php

use Illuminate\Database\Seeder;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        foreach(range(1, 5) as $index)
        {
            DB::table('group_admin')->insert([
                'group_id' => $index,
                'user_id' => $index
            ]);
        }
    }
}
