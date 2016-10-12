<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('admins')) {
            return;
        }
        Schema::create('admins', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string('admin_name', 64)->unique()->comment('用户名');
            $table->string('admin_pwd', 32)->comment('密码');
            $table->string('admin_rand', 32)->comment('随机加密值');
            $table->mediumText('group_id')->nullable()->comment('NULL不属于任何组');
            $table->mediumText('privilege')->nullable()->comment('all是全部权限');
            $table->timestamp('last_time')->nullable()->comment('活跃时间');
            $table->ipAddress('login_ip')->comment('活跃IP');
            $table->tinyInteger('login_num')->comment('尝试登录数');
            $table->timestamp('lock_time')->nullable()->comment('登录锁定时间');
            $table->tinyInteger('is_enable')->comment('是否启用');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('admins')) {
            return;
        }
        Schema::drop('admins');
    }
}
