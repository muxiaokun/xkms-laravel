<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManageUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('manage_uploads')) {
            return;
        }
        Schema::create('manage_uploads', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->integer('user_id')->unsigned()->comment('上传用户');
            $table->integer('user_type')->unsigned()->comment('用户类型');
            $table->string('name', 64)->comment('文件名称');
            $table->string('path', 256)->comment('文件路径');
            $table->string('mime', 64)->comment('mime类型');
            $table->integer('size')->unsigned()->comment('大小');
            $table->string('suffix', 32)->comment('后缀');
            $table->json('bind_info')->nullable()->comment('绑定信息');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('manage_uploads')) {
            return;
        }
        Schema::drop('manage_uploads');
    }
}
