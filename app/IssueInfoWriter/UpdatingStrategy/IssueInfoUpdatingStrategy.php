<?php

namespace App\IssueInfoWriter\UpdatingStrategy;

use App\IssueInfoWriter\TmpIssueInfoTable;

abstract class IssueInfoUpdatingStrategy
{
    abstract public function write(TmpIssueInfoTable $tmpTable);
}
