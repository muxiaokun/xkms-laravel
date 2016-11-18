<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('member_groups')) {
            return;
        }
        Schema::create('member_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->json('manage_id')->nullable()->comment('组管理员');
            $table->string('name', 64)->unique()->comment('分组名称');
            $table->string('explains', 128)->comment('分组说明');
            $table->json('privilege')->nullable()->comment('分组权限');
            $table->tinyInteger('is_enable')->comment('是否启用');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('member_groups')) {
            return;
        }
        Schema::drop('member_groups');
    }
}
