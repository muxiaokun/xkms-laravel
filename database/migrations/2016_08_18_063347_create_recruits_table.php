<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecruitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('recruits')) {
            return;
        }
        Schema::create('recruits', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string('title', 64)->comment('招聘名称');
            $table->mediumText('explains')->nullable()->comment('招聘说明');
            $table->tinyInteger('is_enable')->comment('是否启用');
            $table->integer('current_portion')->unsigned()->comment('当前简历数');
            $table->integer('max_portion')->unsigned()->comment('最大简历数');
            $table->timestamp('start_time')->nullable()->comment('开始时间');
            $table->timestamp('end_time')->nullable()->comment('结束时间');
            $table->json('ext_info')->nullable()->comment('输入项目');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('recruits')) {
            return;
        }
        Schema::drop('recruits');
    }
}
