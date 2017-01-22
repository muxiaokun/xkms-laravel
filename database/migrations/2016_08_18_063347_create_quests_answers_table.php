<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestsAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('quests_answers')) {
            return;
        }
        Schema::create('quests_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->integer('quests_id')->unsigned()->comment('问卷编号');
            $table->integer('member_id')->unsigned()->comment('会员编号');
            $table->longText('answer')->nullable()->comment('答案编号或内容');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('quests_answers')) {
            return;
        }
        Schema::drop('quests_answers');
    }
}
