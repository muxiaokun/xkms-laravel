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
        Schema::create('members', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string('member_name', 64)->unique()->comment('用户名');
            $table->string('member_pwd', 32)->comment('密码');
            $table->string('member_rand', 32)->comment('随机加密值');
            $table->mediumText('group_id')->nullable()->comment('NULL不属于任何组');
            $table->timestamp('register_time')->comment('注册时间');
            $table->timestamp('last_time')->comment('活跃时间');
            $table->ipAddress('login_ip')->comment('活跃IP');
            $table->tinyInteger('login_num')->comment('尝试登录数');
            $table->timestamp('lock_time')->comment('登录锁定时间');
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
        Schema::drop('members');
    }
}
