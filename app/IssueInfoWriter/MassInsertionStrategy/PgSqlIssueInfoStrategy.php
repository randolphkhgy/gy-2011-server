<?php

namespace App\IssueInfoWriter\MassInsertionStrategy;

use App\IssueInfoWriter\PgDataFile;
use Illuminate\Database\Connection;

class PgSqlIssueInfoStrategy extends GenericIssueInfoStrategy
{
    /**
     * @param  array  $array
     * @return $this
     *
     * @throws \Exception
     */
    public function write(array $array = [])
    {
        $data = $this->cleanExists($array);
        ($data) && $this->writeFromFile($this->createPgDataFile($data));
        return $this;
    }

    /**
     * @param  array  $array
     * @return \App\IssueInfoWriter\PgDataFile
     *
     * @throws \Exception
     */
    protected function createPgDataFile(array $array)
    {
        $pgData = new PgDataFile();
        $pgData->write($array)->prepare();
        return $pgData;
    }

    /**
     * @param  \App\IssueInfoWriter\PgDataFile  $pgData
     * @return $this
     */
    protected function writeFromFile(PgDataFile $pgData)
    {
        $db    = $this->getConnection();
        $query = $this->buildQuery($db, $pgData->file(), $pgData->columns());
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
        $copyClause = 'COPY ' . $this->getTable();
        $keyClause  = ' (' . implode(',', $columns). ')';
        $fromClause = ' FROM ' . $db->getPdo()->quote($file);

        return $copyClause . $keyClause . $fromClause;
    }
}
