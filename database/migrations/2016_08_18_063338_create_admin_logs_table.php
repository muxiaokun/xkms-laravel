<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('admin_logs')) {
            return;
        }
        Schema::create('admin_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->integer('admin_id')->unsigned()->comment('管理员编号');
            $table->string('route_name', 128)->nullable()->comment('路由名称');
            $table->string('message', 32)->comment('操作信息');
            $table->mediumText('request')->nullable()->comment('参数');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('admin_logs')) {
            return;
        }
        Schema::drop('admin_logs');
    }
}
