<?php

namespace App\IssueInfoWriter\Strategy;

use App\IssueInfoWriter\CsvFile;
use Illuminate\Database\Connection;

class MySqlIssueInfoStrategy extends IssueInfoWriterStrategy
{
    /**
     * 写入资料.
     *
     * @param  int    $lotteryId
     * @param  array  $array
     * @return $this
     *
     * @throws \Exception
     */
    public function write($lotteryId, array $array = [])
    {
        $csv = new CsvFile();

        $csv->write($lotteryId, $array)->prepare();

        $db = $this->model->getConnection();

        $query = $this->buildQuery($db, $csv->file(), $csv->columns());

        $db->unprepared($query);

        return $this;
    }

    /**
     * 建立 SQL.
     *
     * @param  \Illuminate\Database\Connection  $db
     * @param  string  $file
     * @param  array   $columns
     * @return string
     */
    protected function buildQuery(Connection $db, $file, array $columns)
    {
        $loadDataClause        = 'LOAD DATA LOCAL INFILE ' . $db->getPdo()->quote($file);
        $insertClause          = ' IGNORE INTO TABLE ' . $this->model->getTable();
        $enclosedClause        = ' FIELDS TERMINATED BY \',\'';
        $linesTerminatedClause = ' LINES TERMINATED BY ' . $db->getPdo()->quote(PHP_EOL);
        $columnsClause         = ' (' . implode(',', $columns) . ')';

        return $loadDataClause . $insertClause . $enclosedClause . $linesTerminatedClause . $columnsClause;
    }
}