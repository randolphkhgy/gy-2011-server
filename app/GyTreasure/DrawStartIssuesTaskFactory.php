<?php

namespace App\GyTreasure;

use GyTreasure\Tasks\DrawStartIssuesTask;

class DrawStartIssuesTaskFactory
{
    /**
     * @param  int  $lotteryId
     * @return \GyTreasure\Tasks\DrawStartIssuesTask|null
     */
    public function make($lotteryId)
    {
        $identity = GyTreasureIdentity::getIdentity($lotteryId);
        return $identity ? DrawStartIssuesTask::forge($identity) : null;
    }
}
