<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       
    	$users = [
            ['id' => 1, 'name' => 'Trams Admin', 'email' => 'admin@trams.com', 'password' => bcrypt('admin123'), 'role_id' => 1],
            ['id' => 2, 'name' => 'Trams North Dock Officer', 'email' => 'northdock@trams.com', 'password' => bcrypt('northdock'), 'role_id' => 2],
            ['id' => 3, 'name' => 'Trams Security Officer', 'email' => 'security@trams.com', 'password' => bcrypt('security123'), 'role_id' => 3],
            ['id' => 4, 'name' => 'Trams South Dock Officer', 'email' => 'southdock@trams.com', 'password' => bcrypt('southdock'), 'role_id' => 4],
        ];

        DB::table('users')->insert($users);

        // DB::table('users')->insert([
        //     'name' => 'Trams Admin',
        //     // 'email' => Str::random(10).'@gmail.com',
        //     'email' => 'admin@trams.com',
        //     'password' => bcrypt('secret'),
        // ]);
    }
}
