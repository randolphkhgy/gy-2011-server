<?php

namespace App\IssueInfoWriter\StrategyCommander;

class SqliteIssueInfoWriterCommander extends GenericIssueInfoWriterCommander
{
    /**
     * @param  array  $array
     * @return $this
     */
    public function write(array $array = [])
    {
        // Sqlite 使用 Transaction 同时写入大量资料时可以大幅提高速度.

        $this->getConnection()->beginTransaction();

        parent::write($array);

        $this->getConnection()->commit();

        return $this;
    }
}
