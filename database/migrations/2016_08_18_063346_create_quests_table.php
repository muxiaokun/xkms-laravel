<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quests', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string('title', 64)->comment('标题');
            $table->integer('current_portion')->unsigned()->comment('当前份数');
            $table->integer('max_portion')->unsigned()->comment('最大份数');
            $table->mediumText('start_content')->nullable()->comment('欢迎词');
            $table->mediumText('end_content')->nullable()->comment('结束词');
            $table->timestamp('start_time')->comment('开始时间');
            $table->timestamp('end_time')->comment('结束时间');
            $table->mediumText('access_info')->nullable()->comment('访问配置');
            $table->mediumText('ext_info')->nullable()->comment('后面说明');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('quests');
    }
}
