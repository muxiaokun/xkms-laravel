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
        DB::table('admins')->insert([
            [
                'admin_name' => 'root',
                'admin_pwd'  => 'cb300cf0d7943bb87225d9a105ee9636',
                'admin_rand' => '0',
                'group_id'   => '|1|',
                'privilege'  => json_encode(['all']),
                'is_enable'  => '1',
            ],
            [
                'admin_name' => 'admin',
                'admin_pwd'  => 'b51975874e28ff9170dae62cafe98dfd',
                'admin_rand' => '0',
                'group_id'   => '|2|',
                'privilege'  => json_encode([]),
                'is_enable'  => '1',
            ],
        ]);
    }

}
