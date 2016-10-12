<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('regions')) {
            return;
        }
        Schema::create('regions', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->integer('parent_id')->unsigned()->comment('父编号');
            $table->string('region_name', 64)->comment('地域名称');
            $table->string('short_name', 64)->comment('简写');
            $table->string('all_spell', 64)->comment('全拼');
            $table->string('short_spell', 64)->comment('简拼');
            $table->string('areacode', 64)->comment('区号');
            $table->string('postcode', 64)->comment('邮编');
            $table->tinyInteger('if_show')->comment('是否显示');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('regions')) {
            return;
        }
        Schema::drop('regions');
    }
}
