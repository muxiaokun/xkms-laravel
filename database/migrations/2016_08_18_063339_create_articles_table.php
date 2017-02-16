<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('articles')) {
            return;
        }
        Schema::create('articles', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->integer('user_id')->unsigned()->default(0)->comment('发布人编号');
            $table->longText('access_group_id')->nullable()->comment('可访问会员组');
            $table->string('title', 128)->comment('标题');
            $table->string('author', 64)->comment('作者');
            $table->string('description', 256)->comment('描述');
            $table->mediumText('content')->nullable()->comment('内容');
            $table->integer('cate_id')->unsigned()->comment('分类编号');
            $table->integer('channel_id')->unsigned()->comment('频道编号');
            $table->string('thumb', 256)->comment('缩略图');
            $table->tinyInteger('sort')->comment('排序');
            $table->tinyInteger('is_stick')->comment('是否置顶');
            $table->tinyInteger('if_show')->comment('是否显示');
            $table->integer('is_audit')->unsigned()->comment('审核id 0未审核');
            $table->integer('hits')->unsigned()->default(0)->comment('点击量');
            $table->longText('extend')->nullable()->comment('扩展信息');
            $table->longText('attribute')->nullable()->comment('文章属性');
            $table->longText('album')->nullable()->comment('相册信息');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('articles')) {
            return;
        }
        Schema::drop('articles');
    }
}
