<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->integer('audit_id')->unsigned()->comment('考核表编号');
            $table->integer('send_id')->unsigned()->comment('评分人');
            $table->ipAddress('add_ip')->comment('活跃IP');
            $table->string('controller', 64)->comment('上传控制器');
            $table->integer('item')->unsigned()->comment('属于分组0属于游离');
            $table->tinyInteger('level')->comment('评论级别');
            $table->string('content', 256)->comment('评论内容');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('comments');
    }
}
