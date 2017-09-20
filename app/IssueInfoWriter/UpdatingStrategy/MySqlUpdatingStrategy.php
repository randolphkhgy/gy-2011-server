<?php

namespace App\IssueInfoWriter\UpdatingStrategy;

use App\IssueInfoWriter\TmpIssueInfoTable;

class MySqlUpdatingStrategy extends IssueInfoUpdatingStrategy
{
    /**
     * @param  \App\IssueInfoWriter\TmpIssueInfoTable  $tmpTable
     * @return $this
     */
    public function write(TmpIssueInfoTable $tmpTable)
    {
        $this->insertRows($tmpTable);
        $this->writeCode($tmpTable);
        return $this;
    }

    /**
     * @param  \App\IssueInfoWriter\TmpIssueInfoTable  $tmpTable
     * @return $this
     */
    protected function insertRows(TmpIssueInfoTable $tmpTable)
    {
        $table = $this->getTable();
        $conn  = $this->getConnection();

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
        $table = $this->getTable();
        $conn  = $this->getConnection();

        $updates = [
            'issue.code = tmp.code',
            'issue.writetime = tmp.writetime',
            'issue.writeid = tmp.writeid',
            'issue.statusfetch = tmp.statusfetch',
            'issue.statuscode = tmp.statuscode',
        ];

        $updateClause = 'UPDATE ' . $table . ' AS issue';
        $join1Clause  = ' INNER JOIN ' . $tmpTable->getTable() . ' AS tmp';
        $on1Clause    = ' ON issue.lotteryid = tmp.lotteryid AND issue.issue = tmp.issue';
        $setClause    = ' SET ' . implode(',', $updates);
        $whereClause  = ' WHERE issue.statusfetch = 0 AND tmp.statusfetch <> 0';

        $sql = $updateClause . $join1Clause . $on1Clause . $setClause . $whereClause;

        $conn->unprepared($sql);

        return $this;
    }
}