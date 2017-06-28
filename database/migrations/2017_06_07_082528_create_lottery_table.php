<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLotteryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lottery', function (Blueprint $table) {
            $table->tinyIncrements('lotteryid');
            $table->string('cnname', 20);
            $table->string('enname', 20);
            $table->unsignedInteger('sorts')->default(0);
            $table->tinyInteger('lotterytype')->default(0);
            $table->text('issueset');
            $table->unsignedTinyInteger('weekcycle');
            $table->date('yearlybreakstart');
            $table->date('yearlybreakend');
            $table->float('mincommissiongap', 3, 3);
            $table->float('minprofit', 3, 3);
            $table->string('issuerule', 30);
            $table->text('description');
            $table->text('numberrule');
            $table->unsignedSmallInteger('retry')->default(0);
            $table->unsignedSmallInteger('delay')->default(0);
            $table->unsignedSmallInteger('pushtime')->default(0);
            $table->decimal('lock_insert_set', 12, 4)->nullable();
            $table->decimal('replace_set', 12, 4)->nullable();
            $table->decimal('our_rate_set', 12, 4)->nullable();
            $table->decimal('our_rate_set_max', 12, 4)->nullable();
            $table->decimal('sale_amt_set', 12, 4)->nullable();
            $table->decimal('prize_amt_set', 12, 4)->nullable();
            $table->decimal('our_safe_rate_set', 12, 4)->nullable();
            $table->boolean('unlocked')->default(1);
            $table->char('country', 3)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lottery');
    }
}
