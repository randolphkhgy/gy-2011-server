<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->increments('projectid');
            $table->unsignedInteger('userid');
            $table->integer('packageid');
            $table->unsignedInteger('taskid');
            $table->unsignedTinyInteger('lotteryid');
            $table->unsignedInteger('methodid');
            $table->string('issue', 30);
            $table->decimal('bonus', 14, 4);
            $table->longText('code')->nullable();
            $table->text('codetype');
            $table->decimal('singleprice', 14, 4);
            $table->unsignedInteger('multiple');
            $table->decimal('totalprice', 14, 4);
            $table->unsignedInteger('lvtopid');
            $table->decimal('lvtoppoint', 4, 3);
            $table->unsignedInteger('lvproxyid');
            $table->dateTime('writetime');
            $table->integer('isbroker');
            $table->longText('scode')->nullable();
            $table->timestamp('updatetime');
            $table->dateTime('deducttime');
            $table->dateTime('bonustime');
            $table->dateTime('canceltime');
            $table->boolean('isdeduct')->default(0);
            $table->boolean('iscancel')->default(0);
            $table->boolean('isgetprize')->default(0);
            $table->tinyInteger('prizestatus')->default(0);
            $table->char('userip', 15);
            $table->char('cdnip', 15);
            $table->tinyInteger('modes')->default(0);
            $table->unsignedInteger('sqlnum');
            $table->char('hashvar', 32);
            $table->decimal('userpoint', 4, 3)->default(0);
            $table->boolean('isnew')->default(0);
            $table->string('mmccode', 30)->nullable();
            $table->tinyInteger('comefrom')->default(0);
            $table->string('issue_code_flow', 300)->nullable();

            $table->index('userid');
            $table->index('taskid');
            $table->index(['lotteryid', 'methodid']);
            $table->index('issue');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
