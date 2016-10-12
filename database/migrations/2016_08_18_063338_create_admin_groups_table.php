<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('admin_groups')) {
            return;
        }
        Schema::create('admin_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->mediumText('manage_id')->nullable()->comment('组管理员');
            $table->string('name', 64)->unique()->comment('分组名称');
            $table->string('explains', 128)->comment('分组说明');
            $table->mediumText('privilege')->nullable()->comment('分组权限');
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
        if (!Schema::hasTable('admin_groups')) {
            return;
        }
        Schema::drop('admin_groups');
    }
}
