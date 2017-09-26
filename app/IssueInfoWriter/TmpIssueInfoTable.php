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
     * @var \Illuminate\Database\Connection
     */
    protected $connection;

    /**
     * @var static[]
     */
    protected static $instances = [];

    /**
     * TmpIssueInfoTable constructor.
     * @param string $table
     * @param \Illuminate\Database\Connection $connection
     */
    public function __construct($table, Connection $connection)
    {
        $this->table = $table;
        $this->connection = $connection;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return \Illuminate\Database\Connection
     */
    public function getConnection()
    {
        return $this->connection;
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
     * @return $this
     */
    protected function truncate()
    {
        $this->connection->table($this->getTable())->truncate();
        return $this;
    }

    /**
     * @param  \Illuminate\Database\Connection  $connection
     * @return static
     */
    public static function generate(Connection $connection)
    {
        $connName = $connection->getName();

        if (isset(static::$instances[$connName])) {

            static::$instances[$connName]->truncate();

        } else {

            static::$instances[$connName] = static::newTable($connection);
        }

        return static::$instances[$connName];
    }

    /**
     * @param  \Illuminate\Database\Connection  $connection
     * @return static
     */
    protected static function newTable(Connection $connection)
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

        return new static($tableName, $connection);
    }
}
