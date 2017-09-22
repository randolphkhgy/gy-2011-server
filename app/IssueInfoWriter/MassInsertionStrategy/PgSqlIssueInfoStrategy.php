<?php

namespace App\IssueInfoWriter\MassInsertionStrategy;

use App\IssueInfoWriter\PgDataFile;

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
        ($array) && $this->writeFromFile($this->createPgDataFile($array));
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
        $connection     = $this->getConnection();
        $pdo            = $connection->getPdo();
        $tablePrefix    = (string) $connection->getConfig('prefix');
        $table          = $tablePrefix . $this->getTable();

        $pdo->pgsqlCopyFromFile($table, $pgData->file(), null, null, implode(',', $pgData->columns()));
        return $this;
    }
}
