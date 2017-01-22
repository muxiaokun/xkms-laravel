<?php

use Illuminate\Database\Seeder;

class MemberGroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('member_groups')->insert([
            ['name' => '会员默认分组', 'explains' => '会员默认分组', 'privilege' => json_encode(['all']), 'is_enable' => '1',],
        ]);
    }
}
