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
        if (Schema::hasTable('message_boards')) {
            return;
        }
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
        if (!Schema::hasTable('message_boards')) {
            return;
        }
        Schema::drop('message_boards');
    }
}
