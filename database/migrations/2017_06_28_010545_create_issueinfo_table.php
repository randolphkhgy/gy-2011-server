<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIssueinfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('issueinfo', function (Blueprint $table) {
            $table->increments('issueid');
            $table->unsignedTinyInteger('lotteryid');
            $table->string('code');
            $table->string('issue', 20);
            $table->date('belongdate')->nullable();
            $table->dateTime('salestart');
            $table->dateTime('saleend');
            $table->dateTime('canneldeadline');
            $table->dateTime('earliestwritetime')->nullable();
            $table->dateTime('writetime')->nullable();
            $table->unsignedTinyInteger('writeid')->default(0);
            $table->dateTime('verifytime')->nullable();
            $table->unsignedTinyInteger('verifyid')->default(0);
            $table->unsignedSmallInteger('rank')->default(0);
            $table->unsignedTinyInteger('statusfetch')->default(0);
            $table->unsignedTinyInteger('statuscode')->default(0);
            $table->unsignedTinyInteger('statusdeduct')->default(0);
            $table->unsignedTinyInteger('statususerpoint')->default(0);
            $table->unsignedTinyInteger('statuscheckbonus')->default(0);
            $table->unsignedTinyInteger('statusbonus')->defaut(0);
            $table->unsignedTinyInteger('statustasktoproject')->default(0);
            $table->tinyInteger('statussynced')->default(0);
            $table->tinyInteger('statuslocks')->default(0);
            $table->string('special_code', 32)->default('');
            $table->integer('special_status')->default(0);
            $table->integer('backup_status')->default(0);
            $table->integer('backupdel_status')->default(0);
            $table->dateTime('successtime1')->nullable();
            $table->dateTime('successtime2')->nullable();
            $table->unsignedTinyInteger('error')->default(0);

            $table->unique(['lotteryid', 'issue'], 'lotteryid__issue');
            $table->index(['lotteryid', 'salestart', 'saleend'], 'idx_lottery');
            $table->index('saleend', 'idx_end');
            $table->index(['belongdate', 'saleend', 'lotteryid', 'statuscode'], 'index_union');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('issueinfo');
    }
}
