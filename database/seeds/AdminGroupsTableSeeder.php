<?php

use Illuminate\Database\Seeder;

class AdminGroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admin_groups')->insert([
            ['manage_id' => '|1|', 'name' => 'root', 'explains' => '顶级管理', 'privilege' => 'all', 'is_enable' => '1',],
            ['manage_id' => '|1|', 'name' => 'webadmin', 'explains' => '网站管理', 'privilege' => '', 'is_enable' => '1',],
        ]);
    }
}
