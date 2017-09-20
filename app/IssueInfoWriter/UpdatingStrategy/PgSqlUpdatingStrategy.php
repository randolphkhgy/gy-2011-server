<?php

namespace App\IssueInfoWriter\UpdatingStrategy;

use App\IssueInfoWriter\TmpIssueInfoTable;

class PgSqlUpdatingStrategy extends IssueInfoUpdatingStrategy
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

        $insertClause   = 'INSERT INTO ' . $table;
        $columnsClause  = ' (' . implode(',', $tmpTable->getColumnsWithoutPK()) . ')';
        $selectClause   = ' SELECT ' . implode(',', $tmpTable->getColumnsWithoutPK());
        $fromClause     = ' FROM ' . $tmpTable->getTable();
        $conflictClause = ' ON CONFLICT DO NOTHING';

        $sql = $insertClause . $columnsClause . $selectClause . $fromClause . $conflictClause;

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
            'code = tmp.code',
            'writetime = tmp.writetime',
            'writeid = tmp.writeid',
            'statusfetch = tmp.statusfetch',
            'statuscode = tmp.statuscode',
        ];

        $where = [
            'issue.lotteryid = tmp.lotteryid',
            'issue.issue = tmp.issue',
            'issue.statusfetch = 0',
            'tmp.statusfetch <> 0',
        ];

        $updateClause = 'UPDATE ' . $table . ' AS issue';
        $setClause    = ' SET ' . implode(',', $updates);
        $fromClause   = ' FROM ' . $tmpTable->getTable() . ' AS tmp';
        $whereClause  = ' WHERE ' . implode(' AND ', $where);

        $sql = $updateClause . $setClause . $fromClause . $whereClause;

        $conn->unprepared($sql);

        return $this;
    }
}