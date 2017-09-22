<?php

namespace App\IssueInfoWriter\MassInsertionStrategy;

use App\IssueInfoWriter\CsvFile;
use Illuminate\Database\Connection;

class MySqlIssueInfoStrategy extends GenericIssueInfoStrategy
{
    /**
     * 写入资料.
     *
     * @param  array  $array
     * @return $this
     *
     * @throws \Exception
     */
    public function write(array $array = [])
    {
        if ($this->isLocalInfileAllowed()) {
            // 若允许使用 MySQL 的 LOAD DATA INFILE 可以大幅提高写入速度
            $this->writeByLocalInfile($array);
        } else {
            parent::write($array);
        }

        return $this;
    }

    /**
     * 是否允许使用 MySQL 的 LOAD DATA INFILE 语法.
     *
     * @return bool
     */
    protected function isLocalInfileAllowed()
    {
        // PDO 需要启用 MYSQL_ATTR_LOCAL_INFILE 的选项
        $configLocalInfile = $this->getConnection()->getConfig('options.' . \PDO::MYSQL_ATTR_LOCAL_INFILE);

        // MySQL 的设定需要 local_infile 为 ON
        $GlobalVariable    = array_get(
            $this->getConnection()->getPdo()
                ->query('SHOW GLOBAL VARIABLES LIKE \'local_infile\'')
                ->fetch(\PDO::FETCH_ASSOC),
            'Value',
            'OFF'
        );

        return ($configLocalInfile && strtoupper($GlobalVariable) == 'ON');
    }

    /**
     * 使用 LOAD DATA INFILE 语法写入资料.
     *
     * @param  array  $array
     * @return $this
     *
     * @throws \Exception
     */
    protected function writeByLocalInfile(array $array)
    {
        return $this->writeFromFile($this->createCsvFile($array));
    }

    /**
     * @param  array  $array
     * @return \App\IssueInfoWriter\CsvFile
     *
     * @throws \Exception
     */
    protected function createCsvFile(array $array)
    {
        $csv = new CsvFile();
        $csv->write($array)->prepare();
        return $csv;
    }

    /**
     * @param  \App\IssueInfoWriter\CsvFile  $csv
     * @return $this
     */
    protected function writeFromFile(CsvFile $csv)
    {
        $db    = $this->getConnection();
        $query = $this->buildQuery($db, $csv->file(), $csv->columns());
        $db->unprepared($query);
        return $this;
    }

    /**
     * 建立 SQL.
     *
     * @param  \Illuminate\Database\Connection  $connection
     * @param  string  $file
     * @param  array   $columns
     * @return string
     */
    protected function buildQuery(Connection $connection, $file, array $columns)
    {
        $tablePrefix            = (string) $connection->getConfig('prefix');

        $loadDataClause         = 'LOAD DATA LOCAL INFILE ' . $connection->getPdo()->quote($file);
        $insertClause           = ' INTO TABLE ' . $tablePrefix . $this->getTable();
        $fieldsTerminatedClause = ' FIELDS TERMINATED BY \',\' ENCLOSED BY \'"\' ESCAPED BY \'"\'';
        $linesTerminatedClause  = ' LINES TERMINATED BY ' . $connection->getPdo()->quote(PHP_EOL);
        $columnsClause          = ' (' . implode(',', $columns) . ')';

        return $loadDataClause . $insertClause . $fieldsTerminatedClause . $linesTerminatedClause . $columnsClause;
    }
}
