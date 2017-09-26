<?php

namespace App\IssueInfoWriter\UpdatingStrategy;

use App\IssueInfoWriter\TmpIssueInfoTable;

class SqlSrvUpdatingStrategy extends IssueInfoUpdatingStrategy
{
    /**
     * @param  \App\IssueInfoWriter\TmpIssueInfoTable  $tmpTable
     * @return $this
     */
    public function write(TmpIssueInfoTable $tmpTable)
    {
        $this->updateRows($tmpTable);
        return $this;
    }

    /**
     * @param  \App\IssueInfoWriter\TmpIssueInfoTable  $tmpTable
     * @return $this
     */
    protected function updateRows(TmpIssueInfoTable $tmpTable)
    {
        $table         = $this->getTable();
        $conn          = $this->getConnection();
        $tablePrefix   = (string) $conn->getConfig('prefix');

        $updates = [
            'issue.code = tmp.code',
            'issue.writetime = tmp.writetime',
            'issue.writeid = tmp.writeid',
            'issue.statusfetch = tmp.statusfetch',
            'issue.statuscode = tmp.statuscode',
        ];

        $updateCondition = [
            'issue.statusfetch = 0',
            'tmp.statusfetch <> 0',
        ];

        $mergeClause      = 'MERGE ' . $table . ' AS issue';
        $usingClause      = ' USING ' . $tablePrefix . $tmpTable->getTable() . ' AS tmp';
        $onClause         = ' ON issue.lotteryid = tmp.lotteryid AND issue.issue = tmp.issue';
        $matchedClause    = ' WHEN MATCHED AND ' . implode(' AND ', $updateCondition);
        $matchedThen      = ' THEN UPDATE SET ' . implode(',', $updates);
        $notMatched       = ' WHEN NOT MATCHED';
        $notMatchedKeys   = ' THEN INSERT (' . implode(',', $tmpTable->getColumnsWithoutPK()) . ')';
        $notMatchedValues = ' VALUES(' . implode(',', $this->prefixColumns($tmpTable->getColumnsWithoutPK(), 'tmp.')) . ')';
        $endMergeClause   = ';';

        $sql = $mergeClause . $usingClause . $onClause .
            $matchedClause . $matchedThen . $notMatched .
            $notMatchedKeys . $notMatchedValues . $endMergeClause;

        $conn->unprepared($sql);

        return $this;
    }

    /**
     * 在栏位名称前加上前缀字串
     *
     * @param  array   $columns
     * @param  string  $prefix
     * @return array
     */
    private function prefixColumns($columns, $prefix)
    {
        return preg_filter('/^/', $prefix, $columns);
    }
}
