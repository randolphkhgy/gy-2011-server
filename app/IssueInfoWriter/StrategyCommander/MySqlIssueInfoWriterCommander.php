<?php

namespace App\IssueInfoWriter\StrategyCommander;

use App\IssueInfoWriter\MassInsertionStrategy\MySqlIssueInfoStrategy;
use App\IssueInfoWriter\UpdatingStrategy\MySqlUpdatingStrategy;

class MySqlIssueInfoWriterCommander extends TmpTableWriterCommander
{
    /**
     * @return string
     */
    protected function defaultMassInsertionStrategy()
    {
        return MySqlIssueInfoStrategy::class;
    }

    /**
     * @return string
     */
    protected function defaultUpdatingStrategy()
    {
        return MySqlUpdatingStrategy::class;
    }
}
