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
        if (Schema::hasTable('recruit_logs')) {
            return;
        }
        Schema::create('recruit_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->integer('r_id')->unsigned()->comment('招聘编号');
            $table->string('name', 64)->comment('名字');
            $table->timestamp('birthday')->nullable()->comment('生日');
            $table->tinyInteger('sex')->comment('性别');
            $table->tinyInteger('certificate')->comment('结业证');
            $table->longText('ext_info')->nullable()->comment('扩展信息');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('recruit_logs')) {
            return;
        }
        Schema::drop('recruit_logs');
    }
}
