<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMethodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('method', function (Blueprint $table) {
            $table->increments('methodid');
            $table->integer('pid')->default(0);
            $table->unsignedMediumInteger('lotteryid');
            $table->integer('crowdid')->default(0);
            $table->string('methodname', 20);
            $table->string('code', 20)->nullable()->default('');
            $table->string('jscode', 20)->nullable()->default('');
            $table->boolean('is_special')->default(0);
            $table->integer('addslastype')->default(0);
            $table->string('functionname', 30)->default('');
            $table->string('functionrule')->nullable()->default('');
            $table->string('initlockfunc', 100)->default('');
            $table->text('areatype')->nullable();
            $table->mediumInteger('maxcodecount')->nullable()->default(0);
            $table->tinyInteger('level')->default(1);
            $table->text('nocount')->nullable();
            $table->text('description')->nullable();
            $table->boolean('isclose')->default(0);
            $table->boolean('islock')->default(1);
            $table->string('lockname')->default('');
            $table->decimal('maxlost', 14, 2)->nullable()->default(0);
            $table->decimal('totalmoney', 22, 2)->nullable()->default(0);
            $table->string('modes')->default(0);
            $table->boolean('iscompare')->default(0);
            $table->integer('source_id')->nullable();
            $table->boolean('isbroker')->default(0);

            $table->index('lotteryid', 'idx_lottery');
            $table->index('isclose', 'idx_close');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('method');
    }
}
