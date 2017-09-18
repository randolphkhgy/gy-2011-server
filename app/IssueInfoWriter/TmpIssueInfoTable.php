<?php

namespace App\IssueInfoWriter;

use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;

class TmpIssueInfoTable
{
    /**
     * @var string
     */
    protected $table;

    /**
     * TmpIssueInfoTable constructor.
     * @param string $table
     */
    public function __construct($table)
    {
        $this->table = $table;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return array_merge(['issueid'], $this->getColumnsWithoutPK());
    }

    /**
     * @return array
     */
    public function getColumnsWithoutPK()
    {
        return [
            'lotteryid',
            'code',
            'issue',
            'belongdate',
            'salestart',
            'saleend',
            'canneldeadline',
            'earliestwritetime',
            'writetime',
            'writeid',
            'statusfetch',
            'statuscode',
        ];
    }

    /**
     * @param  \Illuminate\Database\Connection  $connection
     * @return static
     */
    public static function generate(Connection $connection)
    {
        $tableName = uniqid('tmp_issueinfo_');

        $schema = $connection->getSchemaBuilder();
        $schema->create($tableName, function (Blueprint $table) {

            $table->temporary();

            $table->increments('issueid');
            $table->unsignedTinyInteger('lotteryid');
            $table->string('code')->default('');
            $table->string('issue', 20);
            $table->date('belongdate')->nullable();
            $table->dateTime('salestart');
            $table->dateTime('saleend');
            $table->dateTime('canneldeadline');
            $table->dateTime('earliestwritetime')->nullable();
            $table->dateTime('writetime')->nullable();
            $table->unsignedTinyInteger('writeid')->default(0);
            $table->unsignedTinyInteger('statusfetch')->default(0);
            $table->unsignedTinyInteger('statuscode')->default(0);

            $table->unique(['lotteryid', 'issue']);
        });

        return new static($tableName);
    }
}
