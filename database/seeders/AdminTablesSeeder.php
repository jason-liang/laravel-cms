<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class AdminTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        // \Encore\Admin\Auth\Database\Permission::truncate();
        // \Encore\Admin\Auth\Database\Permission::insert(
        //     [
        //         [
        //             "name" => "All permission",
        //             "slug" => "*",
        //             "http_method" => "",
        //             "http_path" => "*"
        //         ],
        //         [
        //             "name" => "Dashboard",
        //             "slug" => "dashboard",
        //             "http_method" => "GET",
        //             "http_path" => "/"
        //         ],
        //         [
        //             "name" => "Login",
        //             "slug" => "auth.login",
        //             "http_method" => "",
        //             "http_path" => "/auth/login\r\n/auth/logout"
        //         ],
        //         [
        //             "name" => "User setting",
        //             "slug" => "auth.setting",
        //             "http_method" => "GET,PUT",
        //             "http_path" => "/auth/setting"
        //         ],
        //         [
        //             "name" => "Auth management",
        //             "slug" => "auth.management",
        //             "http_method" => "",
        //             "http_path" => "/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs"
        //         ]
        //     ]
        // );

        // \Encore\Admin\Auth\Database\Role::truncate();
        // \Encore\Admin\Auth\Database\Role::insert(
        //     [
        //         [
        //             "name" => "Administrator",
        //             "slug" => "administrator"
        //         ]
        //     ]
        // );

        // // pivot tables
        // DB::table('admin_role_menu')->truncate();
        // DB::table('admin_role_menu')->insert(
        //     [
        //         [
        //             "role_id" => 1,
        //             "menu_id" => 2
        //         ]
        //     ]
        // );

        // DB::table('admin_role_permissions')->truncate();
        // DB::table('admin_role_permissions')->insert(
        //     [
        //         [
        //             "role_id" => 1,
        //             "permission_id" => 1
        //         ]
        //     ]
        // );

        // finish
    }
}
