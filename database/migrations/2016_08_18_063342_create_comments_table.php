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
        if (Schema::hasTable('comments')) {
            return;
        }
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->integer('audit_id')->default(0)->unsigned()->comment('审核人');
            $table->integer('send_id')->unsigned()->comment('评论人');
            $table->ipAddress('add_ip')->comment('活跃IP');
            $table->string('route', 64)->comment('上传路由');
            $table->integer('item')->unsigned()->comment('属于分组0属于游离');
            $table->tinyInteger('level')->default(0)->comment('评论级别');
            $table->string('content', 256)->nullable()->comment('评论内容');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('comments')) {
            return;
        }
        Schema::drop('comments');
    }
}
