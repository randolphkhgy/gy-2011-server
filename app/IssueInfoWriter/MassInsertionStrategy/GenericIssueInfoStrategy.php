<?php

namespace App\IssueInfoWriter\MassInsertionStrategy;

use App\IssueInfoWriter\QueryTrait\CountRowsStatementQuery;
use App\IssueInfoWriter\QueryTrait\InsertRowsStatementQuery;

class GenericIssueInfoStrategy extends MassInsertionStrategy
{
    use CountRowsStatementQuery, InsertRowsStatementQuery;

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
     * 清除已存在的资料.
     *
     * @param  array  $data
     * @return array
     */
    protected function cleanExists(array $data)
    {
        $keys = [static::FIELD_LOTTERY_ID, static::FIELD_ISSUE];
        $stmt = $this->buildExistPdoStatement($this->getConnection(), $keys);

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
        $this->insertRows($this->getConnection(), $this->getTable(), $data);
        return $this;
    }
}
