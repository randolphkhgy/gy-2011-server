<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsertreeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usertree', function (Blueprint $table) {
            $table->increments('userid');
            $table->string('username', 20);
            $table->string('nickname', 30)->default('');
            $table->unsignedTinyInteger('usertype');
            $table->unsignedInteger('parentid')->default(0);
            $table->unsignedInteger('lvtopid')->default(0);
            $table->unsignedInteger('lvproxyid')->default(0);
            $table->string('parenttree', 1024)->default('');
            $table->unsignedInteger('userrank')->default(0);
            $table->boolean('isdeleted')->default(0);
            $table->unsignedInteger('deltime')->nullable();
            $table->boolean('isfrozen')->default(0);
            $table->unsignedTinyInteger('frozentype')->default(0);
            $table->boolean('istester')->default(0);
            $table->tinyInteger('ocs_status')->default(0);
            $table->string('flag', 2)->nullable();
            $table->unsignedInteger('frozentime')->nullable()->default(0);
            $table->integer('frozenflag')->default(0);
            $table->string('frozenmemo', 100)->nullable();
            $table->unsignedInteger('lastkickedtime')->nullable();
            $table->integer('fxid')->nullable()->default(1);
            $table->boolean('isblockhistory')->nullable()->default(0);

            $table->unique('username');
            $table->index('usertype');
            $table->index('parentid');
            $table->index('lvtopid');
            $table->index('lvproxyid');
            $table->index('isdeleted');
            // $table->index(['userid', 'parentid', 'parenttree']);
            $table->index(['isfrozen', 'isdeleted']);
            $table->index('flag');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usertree');
    }
}
