<?php

namespace App\IssueInfoWriter\MassInsertionStrategy;

use App\IssueInfoWriter\CsvFile;
use App\IssueInfoWriter\QueryTrait\MySqlInsertIgnoreStatementQuery;
use Illuminate\Database\Connection;

class MySqlIssueInfoStrategy extends GenericIssueInfoStrategy
{
    use MySqlInsertIgnoreStatementQuery;

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
     * @param  array  $array
     * @return array
     */
    protected function data(array $array = [])
    {
        // 若使用 INSERT IGNORE INTO 插入资料, 不再需要清除已存在资料, 故不需要再呼叫 $this->cleanExists
        return $array;
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
        $loadDataClause         = 'LOAD DATA LOCAL INFILE ' . $db->getPdo()->quote($file);
        $insertClause           = ' IGNORE INTO TABLE ' . $this->getTable();
        $fieldsTerminatedClause = ' FIELDS TERMINATED BY \',\' ENCLOSED BY \'"\'';
        $linesTerminatedClause  = ' LINES TERMINATED BY ' . $db->getPdo()->quote(PHP_EOL);
        $columnsClause          = ' (' . implode(',', $columns) . ')';

        return $loadDataClause . $insertClause . $fieldsTerminatedClause . $linesTerminatedClause . $columnsClause;
    }

    /**
     * 建立插入资料 SQL.
     *
     * @param  string  $table
     * @param  array   $fields
     * @return string
     */
    protected function buildInsertQuery($table, array $fields)
    {
        // 若使用 INSERT 方法插入资料，覆写父类别改用 MySQL 的 INSERT IGNORE INTO

        $insertClause = 'INSERT IGNORE INTO ' . $table;
        $keyClause    = ' (' . implode(',', $fields). ')';
        $valueClause  = ' VALUES(' . implode(',', array_map(function ($name) {
                return ':' . $name;
            }, $fields)) . ')';

        return $insertClause . $keyClause . $valueClause;
    }

    /**
     * 执写资料写入.
     *
     * @param  array  $data
     * @return $this
     */
    protected function runInsert(array $data)
    {
        $this->insertIgnoreRows($this->getConnection(), $this->getTable(), $data);
        return $this;
    }
}
