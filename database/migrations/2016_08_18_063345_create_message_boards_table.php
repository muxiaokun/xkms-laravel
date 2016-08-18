<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessageBoardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_boards', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string('name', 64)->comment('名称');
            $table->string('template', 64)->comment('模板');
            $table->mediumText('config')->nullable()->comment('配置');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('message_boards');
    }
}
