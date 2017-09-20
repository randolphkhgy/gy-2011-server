<?php

namespace App\IssueInfoWriter\StrategyCommander;

use App\IssueInfoWriter\MassInsertionStrategy\PgSqlIssueInfoStrategy;
use App\IssueInfoWriter\UpdatingStrategy\PgSqlUpdatingStrategy;

class PgSqlIssueInfoWriterCommander extends TmpTableWriterCommander
{
    /**
     * @return string
     */
    protected function defaultMassInsertionStrategy()
    {
        return PgSqlIssueInfoStrategy::class;
    }

    /**
     * @return string
     */
    protected function defaultUpdatingStrategy()
    {
        return PgSqlUpdatingStrategy::class;
    }
}
