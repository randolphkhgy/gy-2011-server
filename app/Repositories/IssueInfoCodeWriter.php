<?php

namespace App\Repositories;

use App\Models\IssueInfo;
use Carbon\Carbon;

class IssueInfoCodeWriter
{
    /**
     * @var \App\Models\IssueInfo
     */
    protected $model;

    /**
     * IssueInfoCodeWriter constructor.
     * @param \App\Models\IssueInfo $model
     */
    public function __construct(IssueInfo $model)
    {
        $this->model = $model;
    }

    /**
     * @param  int     $lotteryId
     * @param  string  $issue
     * @param  string  $code
     * @return $this
     */
    public function writeCode($lotteryId, $issue, $code)
    {
        $stmt = $this->buildCodeWrittenStatement();
        $this->execStatement($stmt, $lotteryId, $issue, $code);
        return $this;
    }

    /**
     * @param  array  $array
     * @return $this
     */
    public function writeArray(array $array)
    {
        $stmt = $this->buildCodeWrittenStatement();
        foreach ($array as $row) {
            $this->execStatement($stmt, $row['lotteryid'], $row['issue'], $row['code']);
            $stmt->closeCursor();
        }
        return $this;
    }

    /**
     * @param  \PDOStatement  $stmt
     * @param  int     $lotteryId
     * @param  string  $issue
     * @param  string  $code
     * @return $this
     */
    protected function execStatement(\PDOStatement $stmt, $lotteryId, $issue, $code)
    {
        $stmt->bindParam(':lotteryid', $lotteryId, \PDO::PARAM_INT);
        $stmt->bindParam(':issue', $issue, \PDO::PARAM_STR);
        $stmt->bindParam(':code', $code, \PDO::PARAM_STR);
        $stmt->bindValue(':writetime', Carbon::now(), \PDO::PARAM_STR);
        $stmt->execute();
        return $this;
    }

    /**
     * @return \PDOStatement
     */
    protected function buildCodeWrittenStatement()
    {
        $updates = [
            'code = :code',
            'writetime = :writetime',
            'writeid = 255',
            'statusfetch = 2',
            'statuscode = 2',
        ];

        $where = [
            'lotteryid = :lotteryid',
            'issue = :issue',
            'statusfetch = 0',
        ];

        $updateClause = 'UPDATE ' . $this->model->getTable();
        $setClause    = ' SET ' . implode(',', $updates);
        $whereClause  = ' WHERE ' . implode(' AND ', $where);

        $sql = $updateClause . $setClause . $whereClause;
        return $this->model->getConnection()->getPdo()->prepare($sql);
    }
}
