<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessageBoardLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_board_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->integer('msg_id')->unsigned()->comment('留言板id');
            $table->integer('audit_id')->unsigned()->comment('审核id 0未审核');
            $table->integer('send_id')->unsigned()->comment('留言用户');
            $table->ipAddress('add_ip')->comment('发送的IP');
            $table->mediumText('send_info')->nullable()->comment('发送信息');
            $table->mediumText('reply_info')->nullable()->comment('回复信息');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('message_board_logs');
    }
}
