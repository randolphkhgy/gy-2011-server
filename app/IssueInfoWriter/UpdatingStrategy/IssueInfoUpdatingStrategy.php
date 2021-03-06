<?php

namespace App\IssueInfoWriter\UpdatingStrategy;

use App\IssueInfoWriter\TmpIssueInfoTable;
use Illuminate\Database\Connection;

abstract class IssueInfoUpdatingStrategy
{
    /**
     * @var \Illuminate\Database\Connection
     */
    protected $connection;

    /**
     * @var string
     */
    protected $table;

    /**
     * IssueInfoUpdatingStrategy constructor.
     * @param \Illuminate\Database\Connection $connection
     * @param string $table
     */
    public function __construct(Connection $connection, $table)
    {
        $this->connection = $connection;
        $this->table      = $table;
    }

    /**
     * @return \Illuminate\Database\Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param  string  $table
     * @return $this
     */
    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * @param  \App\IssueInfoWriter\TmpIssueInfoTable  $tmpTable
     * @return $this
     */
    abstract public function write(TmpIssueInfoTable $tmpTable);
}
