<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class AddJisupk10ToLotteryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $conn = Schema::getConnection();

        $isSqlServ   = ($conn->getDriverName() == 'sqlsrv');
        $tablePrefix = (string) $conn->getConfig('prefix');

        ($isSqlServ) && $conn->unprepared('SET IDENTITY_INSERT ' . $tablePrefix . 'lottery ON');

        $conn->table('lottery')->insert(
            array (
                'lotteryid' => '107',
                'cnname' => '极速PK10',
                'enname' => 'JSPK10',
                'sorts' => '1',
                'lotterytype' => '0',
                'issueset' => 'a:2:{i:0;a:9:{s:9:"starttime";s:8:"00:00:40";s:12:"firstendtime";s:8:"00:01:40";s:7:"endtime";s:8:"04:59:40";s:5:"cycle";i:60;s:7:"endsale";i:0;s:13:"inputcodetime";i:0;s:8:"droptime";i:0;s:6:"status";i:1;s:4:"sort";i:0;}i:1;a:9:{s:9:"starttime";s:8:"07:00:40";s:12:"firstendtime";s:8:"07:01:40";s:7:"endtime";s:8:"00:00:40";s:5:"cycle";i:60;s:7:"endsale";i:0;s:13:"inputcodetime";i:0;s:8:"droptime";i:0;s:6:"status";i:1;s:4:"sort";i:1;}}',
                'weekcycle' => '127',
                'yearlybreakstart' => '2014-01-30',
                'yearlybreakend' => '2014-02-05',
                'mincommissiongap' => '0.001',
                'minprofit' => '0.020',
                'issuerule' => 'Ymd-[n4]|0,1,0',
                'description' => 'YFPK10',
                'numberrule' => 'a:3:{s:3:"len";s:2:"10";s:7:"startno";s:2:"01";s:5:"endno";s:2:"10";}',
                'retry' => '10',
                'delay' => '31',
                'pushtime' => '25',
                'lock_insert_set' => '0.0000',
                'replace_set' => '0.0000',
                'our_rate_set' => '0.0000',
                'our_rate_set_max' => '0.0000',
                'sale_amt_set' => '0.0000',
                'prize_amt_set' => '0.0000',
                'our_safe_rate_set' => '0.0000',
                'unlocked' => '1',
                'country' => '1',
            )
        );

        ($isSqlServ) && $conn->unprepared('SET IDENTITY_INSERT ' . $tablePrefix . 'lottery OFF');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::getConnection()->table('lottery')->where('lotteryid', '107')->delete();
    }
}
