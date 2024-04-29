<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		Db::table('users')->insert([
			'name' => env('ADMIN_NAME'),
			'email' => env('ADMIN_EMAIL'),
			'email_verified_at' => now(),
			'password' => bcrypt(env('ADMIN_PASSWORD')),
		]);
		DB::table('users')->insert([
			'name' => env('ADMIN_NAME2'),
			'email' => env('ADMIN_EMAIL2'),
			'email_verified_at' => now(),
			'password' => bcrypt(env('ADMIN_PASSWORD2')),
		]);
    }
}
