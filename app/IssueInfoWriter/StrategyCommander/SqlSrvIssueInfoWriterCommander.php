<?php

namespace App\IssueInfoWriter\StrategyCommander;

use App\IssueInfoWriter\MassInsertionStrategy\GenericIssueInfoStrategy;
use App\IssueInfoWriter\UpdatingStrategy\SqlSrvUpdatingStrategy;

class SqlSrvIssueInfoWriterCommander extends TmpTableWriterCommander
{
    /**
     * @var bool
     */
    protected $useTransaction = true;

    /**
     * @return string
     */
    protected function defaultMassInsertionStrategy()
    {
        return GenericIssueInfoStrategy::class;
    }

    /**
     * @return string
     */
    protected function defaultUpdatingStrategy()
    {
        return SqlSrvUpdatingStrategy::class;
    }
}
