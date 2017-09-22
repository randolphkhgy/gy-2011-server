<?php

namespace App\IssueInfoWriter\StrategyCommander;

use App\IssueInfoWriter\QueryTrait\CountRowsStatementQuery;
use App\IssueInfoWriter\QueryTrait\InsertRowsStatementQuery;
use Illuminate\Database\Connection;

class GenericIssueInfoWriterCommander extends IssueInfoWriterCommander
{
    use CountRowsStatementQuery, InsertRowsStatementQuery;

    const FIELD_LOTTERY_ID = 'lotteryid';
    const FIELD_ISSUE      = 'issue';

    /**
     * @param  array  $array
     * @return $this
     */
    public function write(array $array = [])
    {
        list($insertions, $others) = $this->splitInsertions($array);

        $this->runInsert($insertions->toArray());
        $this->runUpdate($others->toArray());

        return $this;
    }

    /**
     * @param  array  $array
     * @return \Illuminate\Support\Collection
     */
    protected function splitInsertions(array $array)
    {
        $keys = [static::FIELD_LOTTERY_ID, static::FIELD_ISSUE];
        $stmt = $this->buildExistPdoStatement($this->getConnection(), $keys);

        return collect($array)->partition(function ($row) use ($stmt) {
            $stmt->bindParam(':' . static::FIELD_LOTTERY_ID, $row[static::FIELD_LOTTERY_ID]);
            $stmt->bindParam(':' . static::FIELD_ISSUE, $row[static::FIELD_ISSUE]);

            $stmt->execute();

            $exists = $stmt->fetchColumn();

            $stmt->closeCursor();

            return (! $exists);
        });
    }

    /**
     * @param  array  $array
     * @return $this
     */
    protected function runInsert(array $array)
    {
        return $this->insertRows($this->getConnection(), $this->getTable(), $array);
    }

    /**
     * @param  array  $array
     * @return $this
     */
    protected function runUpdate(array $array)
    {
        $columns = ['code', 'writetime', 'writeid', 'statusfetch', 'statuscode'];

        $stmt = $this->buildUpdatePdoStatement($this->getConnection(), $this->getTable(), $columns);

        foreach ($array as $row)
        {
            if (array_get($row, 'statusfetch', 0) != 0) {

                $stmt->bindValue(':' . static::FIELD_LOTTERY_ID, array_get($row, static::FIELD_LOTTERY_ID));
                $stmt->bindValue(':' . static::FIELD_ISSUE, array_get($row, static::FIELD_ISSUE));

                foreach ($columns as $key) {
                    $stmt->bindValue(':' . $key, array_get($row, $key));
                }

                $stmt->execute();

                $stmt->closeCursor();
            }
        }

        return $this;
    }

    /**
     * @param  \Illuminate\Database\Connection $connection
     * @param  string  $table
     * @param  array   $columns
     * @return \PDOStatement
     */
    protected function buildUpdatePdoStatement(Connection $connection, $table, array $columns)
    {
        $pdo = $connection->getPdo();
        return $pdo->prepare($this->buildUpdateQuery($connection, $table, $columns));
    }

    /**
     * @param  \Illuminate\Database\Connection $connection
     * @param  string  $table
     * @param  array   $columns
     * @return string
     */
    protected function buildUpdateQuery(Connection $connection, $table, array $columns)
    {
        $tablePrefix  = (string) $connection->getConfig('prefix');

        $where = [
            static::FIELD_LOTTERY_ID . ' = :' . static::FIELD_LOTTERY_ID,
            static::FIELD_ISSUE . ' = :' . static::FIELD_ISSUE,
            'statusfetch = 0'
        ];

        $updateClause = 'UPDATE ' . $tablePrefix . $table;
        $setClause    = ' SET ' . implode(',', array_map(function ($key) {
            return $key . ' = :' . $key;
        }, $columns));
        $whereClause  = ' WHERE ' . implode(' AND ', $where);

        return $updateClause . $setClause . $whereClause;
    }
}
