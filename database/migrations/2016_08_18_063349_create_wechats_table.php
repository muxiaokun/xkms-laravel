<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWechatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('wechats')) {
            return;
        }
        Schema::create('wechats', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string('openid', 64)->comment('公众账号唯一id');
            $table->integer('member_id')->unsigned()->comment('系统会员编号');
            $table->string('unionid', 64)->comment('全局唯一id');
            $table->string('nickname', 64)->comment('昵称');
            $table->tinyInteger('sex')->nullable()->comment('性别');
            $table->string('country', 64)->nullable()->comment('国家');
            $table->string('province', 64)->nullable()->comment('省市');
            $table->string('city', 64)->nullable()->comment('城市');
            $table->string('language', 64)->nullable()->comment('语言');
            $table->string('headimgurl', 256)->nullable()->comment('头像46、64、96、132 ');
            $table->timestamp('bind_time')->nullable()->comment('bind_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('wechats')) {
            return;
        }
        Schema::drop('wechats');
    }
}
