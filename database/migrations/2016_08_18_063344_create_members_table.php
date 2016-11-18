<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('members')) {
            return;
        }
        Schema::create('members', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string('member_name', 64)->unique()->comment('用户名');
            $table->string('member_pwd', 32)->comment('密码');
            $table->string('member_rand', 32)->comment('随机加密值');
            $table->json('group_id')->nullable()->comment('NULL不属于任何组');
            $table->timestamp('last_time')->nullable()->comment('活跃时间');
            $table->ipAddress('login_ip')->nullable()->comment('活跃IP');
            $table->tinyInteger('login_num')->comment('尝试登录数');
            $table->timestamp('lock_time')->nullable()->comment('登录锁定时间');
            $table->tinyInteger('is_enable')->comment('是否启用');
            $table->string('email', 64)->comment('电邮');
            $table->string('phone', 64)->comment('手机');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('members')) {
            return;
        }
        Schema::drop('members');
    }
}
