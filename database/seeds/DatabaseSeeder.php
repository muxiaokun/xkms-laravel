<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call(AdminsTableSeeder::class);
        $this->call(AdminGroupsTableSeeder::class);
        $this->call(MemberGroupsTableSeeder::class);
        $this->call(MessageBoardsTableSeeder::class);
        $this->call(RegionsTableSeeder::class);
    }
}
