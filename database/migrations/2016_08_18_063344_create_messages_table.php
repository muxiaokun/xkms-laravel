<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->integer('send_id')->unsigned()->comment('0为系统发送');
            $table->integer('receive_id')->unsigned()->comment('0为系统接收');
            $table->timestamp('send_time')->comment('发送时间');
            $table->timestamp('receive_time')->comment('查看时间');
            $table->mediumText('content')->nullable()->comment('发送的内容');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('messages');
    }
}
