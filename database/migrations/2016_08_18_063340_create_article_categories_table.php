<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('article_categories')) {
            return;
        }
        Schema::create('article_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->integer('parent_id')->unsigned()->comment('分类父ID');
            $table->string('name', 64)->comment('名称');
            $table->json('manage_id')->nullable()->comment('分类管理员');
            $table->json('manage_group_id')->nullable()->comment('分类管理组');
            $table->json('access_group_id')->nullable()->comment('可访问会员组');
            $table->string('thumb', 256)->comment('缩略图');
            $table->tinyInteger('sort')->comment('排序');
            $table->tinyInteger('if_show')->comment('是否显示');
            $table->tinyInteger('is_content')->comment('启用内容');
            $table->tinyInteger('s_limit')->comment('查询数量');
            $table->mediumText('content')->nullable()->comment('内容');
            $table->json('extend')->nullable()->comment('扩展配置');
            $table->json('attribute')->nullable()->comment('属性配置');
            $table->string('template', 64)->comment('分类模板');
            $table->string('list_template', 64)->comment('管理模板');
            $table->string('article_template', 64)->comment('内容模板');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('article_categories')) {
            return;
        }
        Schema::drop('article_categories');
    }
}
