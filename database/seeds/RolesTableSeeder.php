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
            ['id' => 2, 'name' => 'North Dock Officer', 'description' => 'North','submodules' => 'PCC 1|PCC 2|
'],
            ['id' => 3, 'name' => 'Security Officer', 'description' => 'Security Officer'],
            ['id' => 4, 'name' => 'South Dock Officer', 'description' => 'South','submodules' => 'Fem Care|Laundry
'],
        ];

        DB::table('roles')->insert($roles);
    }
}
