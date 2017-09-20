<?php

namespace App\IssueInfoWriter\QueryTrait;

use Illuminate\Database\Connection;

trait InsertRowsStatementQuery
{
    /**
     * 建立插入资料 PDOStatement 物件.
     *
     * @param  \Illuminate\Database\Connection  $connection
     * @param  string  $table
     * @param  array   $fields
     * @return \PDOStatement
     */
    protected function buildInsertPdoStatement(Connection $connection, $table, array $fields)
    {
        $pdo = $connection->getPdo();
        return $pdo->prepare($this->buildInsertQuery($table, $fields));
    }

    /**
     * 建立插入资料 SQL.
     *
     * @param  string  $table
     * @param  array   $fields
     * @return string
     */
    protected function buildInsertQuery($table, array $fields)
    {
        $insertClause = 'INSERT INTO ' . $table;
        $keyClause    = ' (' . implode(',', $fields). ')';
        $valueClause  = ' VALUES(' . implode(',', array_map(function ($name) {
                return ':' . $name;
            }, $fields)) . ')';

        return $insertClause . $keyClause . $valueClause;
    }

    /**
     * @param  \Illuminate\Database\Connection  $connection
     * @param  string  $table
     * @param  array   $row
     * @return $this
     */
    protected function insertRow(Connection $connection, $table, array $row)
    {
        $stmt = $this->buildInsertPdoStatement($connection, $table, array_keys($row));
        foreach ($row as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->execute();
        return $this;
    }

    /**
     * 执写资料写入.
     *
     * @param  \Illuminate\Database\Connection  $connection
     * @param  string  $table
     * @param  array   $data
     * @return $this
     */
    protected function insertRows(Connection $connection, $table, array $data)
    {
        // 无资料不用处理
        if (! $data) {
            return $this;
        }

        $fields = array_keys(head($data));
        $stmt   = $this->buildInsertPdoStatement($connection, $table, $fields);
        foreach ($data as $row) {

            foreach ($row as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }

            $stmt->execute();
            $stmt->closeCursor();
        }
        return $this;
    }
}