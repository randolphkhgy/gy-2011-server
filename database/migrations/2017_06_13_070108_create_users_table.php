<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('userid');
            $table->string('username', 20);
            $table->char('loginpwd', 32);
            $table->char('loginpwd_salt', 8)->nullable();
            $table->char('securitypwd', 32);
            $table->char('securitypwd_salt', 8)->nullable();
            $table->unsignedTinyInteger('usertype')->default(0);
            $table->string('nickname', 30)->default('');
            $table->string('language', 10)->default('utf8_zhcn');
            $table->string('skin', 10)->default('new');
            $table->string('email', 60)->nullable()->default('');
            $table->string('email_old', 60)->default('');
            $table->string('mobile_phone', 64)->nullable();
            $table->string('surename', 192)->nullable();
            $table->string('country', 10)->default(0);
            $table->string('qq', 20)->nullable();
            $table->boolean('authtoparent')->default(0);
            $table->integer('addcount')->default(0);
            $table->boolean('authadd')->default(0);
            $table->char('lastip', 15);
            $table->datetime('lasttime');
            $table->char('registerip', 15);
            $table->datetime('registertime');
            $table->unsignedInteger('userrank')->default(0);
            $table->datetime('rankcreatetime')->nullable();
            $table->datetime('rankupdate')->nullable();
            $table->unsignedSmallInteger('question_id_1')->default(0);
            $table->string('define_question_1', 100)->default('');
            $table->string('answer_1', 255)->default('');
            $table->unsignedSmallInteger('question_id_2')->default(0);
            $table->string('define_question_2', 100)->default('');
            $table->string('answer_2', 255)->default('');
            $table->float('keeppoint', 5, 3)->nullable();
            $table->boolean('blockuser')->default(0);
            $table->integer('errorcount')->nullable()->default(0);
            $table->datetime('lasterrtime')->nullable();
            $table->unsignedTinyInteger('user_lang')->default(1);

            $table->unique('username');
            $table->index(['userid', 'username']);
            $table->index(['username', 'loginpwd', 'securitypwd']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
