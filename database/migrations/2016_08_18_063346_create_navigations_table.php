<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNavigationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('navigations')) {
            return;
        }
        Schema::create('navigations', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string('name', 128)->comment('名称');
            $table->string('short_name', 32)->comment('调用短名');
            $table->tinyInteger('is_enable')->comment('是否启用');
            $table->json('ext_info')->nullable()->comment('扩展信息');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('navigations')) {
            return;
        }
        Schema::drop('navigations');
    }
}
