<?php

namespace App\GyTreasure;

use GyTreasure\Tasks\DrawIssueTask;

class DrawIssueTaskFactory
{
    /**
     * @param  int  $lotteryId
     * @return \GyTreasure\Tasks\DrawIssueTask|null
     */
    public function make($lotteryId)
    {
        $identity = GyTreasureIdentity::getIdentity($lotteryId);
        return $identity ? DrawIssueTask::forge($identity) : null;
    }
}
