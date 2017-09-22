<?php

namespace App\IssueInfoWriter\QueryTrait;

use Illuminate\Database\Connection;

trait CountRowsStatementQuery
{
    /**
     * 建立查询资料笔数 PDOStatement 物件.
     *
     * @param  \Illuminate\Database\Connection $connection
     * @param  array  $keys
     * @return \PDOStatement
     */
    protected function buildCountRowsPdoStatement(Connection $connection, array $keys)
    {
        $pdo = $connection->getPdo();
        return $pdo->prepare($this->buildCountRowsQuery($connection, $this->getTable(), $keys));
    }

    /**
     * 建立查询资料笔数 SQL.
     *
     * @param  \Illuminate\Database\Connection $connection
     * @param  string  $table
     * @param  array   $keys
     * @return string
     */
    protected function buildCountRowsQuery(Connection $connection, $table, array $keys)
    {
        $tablePrefix  = (string) $connection->getConfig('prefix');

        $selectClause = 'SELECT COUNT(*) AS count FROM ' . $tablePrefix . $table;
        $whereClause  = ' WHERE ' . implode(' AND ', array_map(function ($key) {
                return $key . ' = :' . $key;
            }, $keys));

        return $selectClause . $whereClause;
    }

    /**
     * 建立查询资料是否已存在的 PDOStatement 物件.
     *
     * @param  \Illuminate\Database\Connection $connection
     * @param  array  $keys
     * @return \PDOStatement
     */
    protected function buildExistPdoStatement(Connection $connection, array $keys)
    {
        $pdo = $connection->getPdo();
        return $pdo->prepare($this->buildExistQuery($connection, $this->getTable(), $keys));
    }

    /**
     * 建立查询资料是否已存在 SQL.
     *
     * @param  \Illuminate\Database\Connection $connection
     * @param  string  $table
     * @param  array   $keys
     * @return string
     */
    protected function buildExistQuery(Connection $connection, $table, array $keys)
    {
        // 使用 count 的 SQL.
        return $this->buildCountRowsQuery($connection, $table, $keys);
    }
}
