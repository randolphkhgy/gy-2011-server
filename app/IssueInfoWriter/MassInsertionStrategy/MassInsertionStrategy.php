<?php

namespace App\IssueInfoWriter\MassInsertionStrategy;

use Illuminate\Database\Connection;

abstract class MassInsertionStrategy
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
     * MassInsertionStrategy constructor.
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
     * 写入资料.
     *
     * @param  array  $array
     * @return $this
     */
    abstract public function write(array $array = []);
}
