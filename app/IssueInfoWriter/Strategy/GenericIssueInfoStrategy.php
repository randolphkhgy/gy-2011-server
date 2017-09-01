<?php

namespace App\IssueInfoWriter\Strategy;

class GenericIssueInfoStrategy extends IssueInfoWriterStrategy
{
    const FIELD_LOTTERY_ID = 'lotteryid';
    const FIELD_ISSUE      = 'issue';

    /**
     * 写入资料.
     *
     * @param  array  $array
     * @return $this
     */
    public function write(array $array = [])
    {
        $data = $this->data($array);

        $this->runInsert($data);

        return $this;
    }

    /**
     * @param  array  $array
     * @return array
     */
    protected function data(array $array = [])
    {
        return $this->cleanExists($array);
    }

    /**
     * 建立插入资料 PDOStatement 物件.
     *
     * @param  array  $fields
     * @return \PDOStatement
     */
    protected function buildInsertPdoStatement(array $fields)
    {
        $pdo = $this->model->getConnection()->getPdo();
        return $pdo->prepare($this->buildInsertQuery($this->model->getTable(), $fields));
    }

    /**
     * 建立查询资料是否已存在的 PDOStatement 物件.
     *
     * @param  array  $keys
     * @return \PDOStatement
     */
    protected function buildExistPdoStatement(array $keys)
    {
        $pdo = $this->model->getConnection()->getPdo();
        return $pdo->prepare($this->buildExistQuery($this->model->getTable(), $keys));
    }

    /**
     * 建立查询资料是否已存在 SQL.
     *
     * @param  string  $table
     * @param  array   $keys
     * @return string
     */
    protected function buildExistQuery($table, array $keys)
    {
        $selectClause = 'SELECT COUNT(*) AS count FROM ' . $table;
        $whereClause  = ' WHERE ' . implode(' AND ', array_map(function ($key) {
            return $key . ' = :' . $key;
        }, $keys));

        return $selectClause . $whereClause;
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
     * 清除已存在的资料.
     *
     * @param  array  $data
     * @return array
     */
    protected function cleanExists(array $data)
    {
        $keys = [static::FIELD_LOTTERY_ID, static::FIELD_ISSUE];
        $stmt = $this->buildExistPdoStatement($keys);

        return array_filter($data, function ($row) use ($stmt) {
            $stmt->bindParam(':' . static::FIELD_LOTTERY_ID, $row[static::FIELD_LOTTERY_ID]);
            $stmt->bindParam(':' . static::FIELD_ISSUE, $row[static::FIELD_ISSUE]);

            $stmt->execute();

            $exists = $stmt->fetchColumn();

            $stmt->closeCursor();

            return (! $exists);
        });
    }

    /**
     * 执写资料写入.
     *
     * @param  array  $data
     * @return $this
     */
    protected function runInsert(array $data)
    {
        // 无资料不用处理
        if (! $data) {
            return $this;
        }

        $fields = array_keys(head($data));
        $stmt   = $this->buildInsertPdoStatement($fields);
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
