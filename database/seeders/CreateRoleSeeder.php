<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            ['role_name'=> 'admin', 'display_name'=>' Quản trị hệ thống'],
            ['role_name'=> 'guest', 'display_name'=>' Khách hàng'],
            ['role_name'=> 'developer', 'display_name'=>' Phát triển hệ thống'],
            ['role_name'=> 'content', 'display_name'=>' Edit, update nội dung'],

        ]);
    }
    //php artisan db:seed --class=roles
}
