<?php

namespace App\IssueInfoWriter\UpdatingStrategy;

use App\IssueInfoWriter\TmpIssueInfoTable;
use App\Models\IssueInfo;

class GenericIssueInfoUpdatingStrategy
{
    /**
     * @var \App\Models\IssueInfo
     */
    protected $model;

    /**
     * @param \App\Models\IssueInfo $model
     */
    public function __construct(IssueInfo $model)
    {
        $this->model = $model;
    }

    public function write(TmpIssueInfoTable $tmpTable)
    {
        $this->insertRows($tmpTable);
        $this->writeCode($tmpTable);
    }

    /**
     * @param  \App\IssueInfoWriter\TmpIssueInfoTable  $tmpTable
     * @return $this
     */
    protected function insertRows(TmpIssueInfoTable $tmpTable)
    {
        $table = $this->model->getTable();
        $conn  = $this->model->getConnection();

        $insertClause  = 'INSERT IGNORE INTO ' . $table;
        $columnsClause = ' (' . implode(',', $tmpTable->getColumnsWithoutPK()) . ')';
        $selectClause  = ' SELECT ' . implode(',', $tmpTable->getColumnsWithoutPK());
        $fromClause    = ' FROM ' . $tmpTable->getTable();

        $sql = $insertClause . $columnsClause . $selectClause . $fromClause;

        $conn->unprepared($sql);

        return $this;
    }

    /**
     * @param  \App\IssueInfoWriter\TmpIssueInfoTable  $tmpTable
     * @return $this
     */
    protected function writeCode(TmpIssueInfoTable $tmpTable)
    {
        $table = $this->model->getTable();
        $conn  = $this->model->getConnection();

        $updates = [
            'is.code = tmp.code',
            'is.writetime = tmp.writetime',
            'is.writeid = tmp.writeid',
            'is.statuscode = tmp.statuscode',
        ];

        $updateClause = 'UPDATE ' . $table . ' AS is';
        $join1Clause  = ' INNER JOIN ' . $tmpTable->getTable() . ' AS tmp';
        $on1Clause    = ' ON is.lotteryid = tmp.lotteryid AND is.issue = tmp.issue';
        $setClause    = ' SET ' . implode(',', $updates);
        $whereClause  = ' WHERE is.statusfetch = 0';

        $sql = $updateClause . $join1Clause . $on1Clause . $setClause . $whereClause;

        $conn->unprepared($sql);

        return $this;
    }
}
