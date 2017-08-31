<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ImportMethodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        switch (DB::getDriverName()) {
            case 'mysql':
                $this->mysqlImport($this->csv());
                break;
           case 'pgsql':
               $this->pgImport($this->pgSql());
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
        DB::table('method')->truncate();
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
        return database_path('pg_migration_method_table.sql');
    }

    protected function mysqlImport($file)
    {
        $loadClause   = 'LOAD DATA LOCAL INFILE ' . DB::getPdo()->quote($file);
        $insertClause = ' INTO TABLE method';
        $fieldClause  = ' FIELDS TERMINATED BY \',\' ENCLOSED BY \'"\' ESCAPED BY \'"\'';
        $linesClause  = ' LINES TERMINATED BY \'\n\'';

        $query = $loadClause . $insertClause . $fieldClause . $linesClause;
        DB::unprepared($query);
    }

    protected function pgImport($file)
    {
        DB::unprepared(file_get_contents($file));
    }
}
