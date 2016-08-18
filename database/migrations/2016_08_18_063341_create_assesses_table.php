<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assesses', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string('title', 64)->comment('考核表名称');
            $table->string('explains', 256)->comment('考核说明');
            $table->tinyInteger('group_level')->comment('定义考核分组级别');
            $table->integer('start_time')->unsigned()->comment('开始时间');
            $table->integer('end_time')->unsigned()->comment('结束时间');
            $table->tinyInteger('is_enable')->comment('是否启用');
            $table->string('target', 64)->comment('考核目标');
            $table->mediumText('ext_info')->nullable()->comment('条目数组');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('assesses');
    }
}
