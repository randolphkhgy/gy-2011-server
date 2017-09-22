<?php

namespace App\IssueInfoWriter\MassInsertionStrategy;

use App\IssueInfoWriter\QueryTrait\CountRowsStatementQuery;
use App\IssueInfoWriter\QueryTrait\InsertRowsStatementQuery;

class GenericIssueInfoStrategy extends MassInsertionStrategy
{
    use CountRowsStatementQuery, InsertRowsStatementQuery;

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
        return $array;
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
