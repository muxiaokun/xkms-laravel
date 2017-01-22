<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('article_channels')) {
            return;
        }
        Schema::create('article_channels', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string('name', 64)->comment('名称');
            $table->longText('manage_id')->nullable()->comment('频道管理员');
            $table->longText('manage_group_id')->nullable()->comment('频道管理组');
            $table->longText('access_group_id')->nullable()->comment('可访问会员组');
            $table->tinyInteger('if_show')->comment('是否显示');
            $table->string('template', 64)->comment('频道首页模板');
            $table->mediumText('keywords')->nullable()->comment('关键字');
            $table->mediumText('description')->nullable()->comment('描述');
            $table->mediumText('other')->nullable()->comment('其他');
            $table->longText('extend')->nullable()->comment('扩展配置');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('article_channels')) {
            return;
        }
        Schema::drop('article_channels');
    }
}
