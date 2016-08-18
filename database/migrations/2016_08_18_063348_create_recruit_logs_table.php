<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecruitLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recruit_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->integer('r_id')->unsigned()->comment('招聘编号');
            $table->string('name', 64)->comment('名字');
            $table->timestamp('birthday')->comment('生日');
            $table->tinyInteger('sex')->comment('性别');
            $table->tinyInteger('certificate')->comment('结业证');
            $table->mediumText('ext_info')->nullable()->comment('扩展信息');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('recruit_logs');
    }
}
