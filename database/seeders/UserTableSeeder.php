<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'user_name' => 'admin',
            'user_role' => 'admin',
            'password' => bcrypt('admin'),
            'otpverified' => 1,
        ]);
    }
}
