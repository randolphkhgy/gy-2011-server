<?php

namespace App\GyTreasure;

use GyTreasure\Tasks\DrawDateTask;

class DrawDateTaskFactory
{
    /**
     * @param  int  $lotteryId
     * @return \GyTreasure\Tasks\DrawDateTask|null
     */
    public function make($lotteryId)
    {
        $identity = GyTreasureIdentity::getIdentity($lotteryId);
        return $identity ? DrawDateTask::forge($identity) : null;
    }
}
