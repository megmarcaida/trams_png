<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$roles = [
            ['id' => 1, 'name' => 'Admin', 'description' => 'Administrator'],
            ['id' => 2, 'name' => 'Docker Officer', 'description' => 'Docker Officer'],
            ['id' => 3, 'name' => 'Security Officer', 'description' => 'Security Officer'],
        ];

        DB::table('roles')->insert($roles);
    }
}
