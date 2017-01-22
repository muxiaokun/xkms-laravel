<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItlinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('itlinks')) {
            return;
        }
        Schema::create('itlinks', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string('name', 128)->comment('名称');
            $table->string('short_name', 32)->comment('调用短名');
            $table->tinyInteger('is_enable')->comment('是否启用');
            $table->timestamp('start_time')->nullable()->comment('开始时间');
            $table->timestamp('end_time')->nullable()->comment('结束时间');
            $table->integer('max_show_num')->unsigned()->comment('最大显示数量');
            $table->integer('show_num')->unsigned()->comment('显示数量');
            $table->integer('max_hit_num')->unsigned()->comment('最大点击数');
            $table->integer('hit_num')->unsigned()->comment('点击数');
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
        if (!Schema::hasTable('itlinks')) {
            return;
        }
        Schema::drop('itlinks');
    }
}
