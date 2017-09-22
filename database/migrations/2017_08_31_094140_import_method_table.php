<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class ImportMethodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        switch (Schema::getConnection()->getDriverName()) {
            case 'mysql':
                $this->mysqlImport($this->csv());
                break;
            case 'pgsql':
                $this->pgImport($this->pgSql());
                break;
            default:
                $this->standardSqlImport($this->standardSql());
                break;
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::getConnection()->table('method')->truncate();
    }

    /**
     * @return string
     */
    protected function csv()
    {
        return database_path('migration_method_table.csv');
    }

    /**
     * @return string
     */
    protected function pgSql()
    {
        return database_path('pg_migration_method_table.data');
    }

    /**
     * @return string
     */
    protected function standardSql()
    {
        return database_path('pg_migration_method_table.sql');
    }

    /**
     * @param string $file
     */
    protected function mysqlImport($file)
    {
        $connection   = Schema::getConnection();

        $tablePrefix  = (string) $connection->getConfig('prefix');
        $table        = $tablePrefix . 'method';

        $loadClause   = 'LOAD DATA LOCAL INFILE ' . $connection->getPdo()->quote($file);
        $insertClause = ' INTO TABLE ' . $table;
        $fieldClause  = ' FIELDS TERMINATED BY \',\' ENCLOSED BY \'"\' ESCAPED BY \'"\'';
        $linesClause  = ' LINES TERMINATED BY \'\n\'';

        $query = $loadClause . $insertClause . $fieldClause . $linesClause;
        $connection->unprepared($query);
    }

    /**
     * @param string $file
     */
    protected function pgImport($file)
    {
        $tablePrefix  = (string) Schema::getConnection()->getConfig('prefix');
        $table        = $tablePrefix . 'method';
        Schema::getConnection()->getPdo()->pgsqlCopyFromFile($table, $file);
    }

    /**
     * @param string $file
     */
    protected function standardSqlImport($file)
    {
        $conn         = Schema::getConnection();
        $tablePrefix  = (string) $conn->getConfig('prefix');
        $table        = $tablePrefix . 'method';

        $sql          = file_get_contents($file);
        if ($table != 'method') {
            $sql      = str_replace('INSERT INTO method ', "INSERT INTO $table ", $sql);
        }

        $conn->beginTransaction();
        $conn->unprepared($sql);
        $conn->commit();
    }
}
