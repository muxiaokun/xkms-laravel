<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssessLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('assess_logs')) {
            return;
        }
        Schema::create('assess_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->integer('assess_id')->unsigned()->comment('考核表编号');
            $table->integer('grade_id')->unsigned()->comment('评分人');
            $table->integer('re_grade_id')->unsigned()->comment('被评分人');
            $table->timestamp('add_time')->nullable()->comment('添加时间');
            $table->longText('score')->nullable()->comment('评分数组');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('assess_logs')) {
            return;
        }
        Schema::drop('assess_logs');
    }
}
