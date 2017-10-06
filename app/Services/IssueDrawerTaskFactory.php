<?php

namespace App\Services;

use App\GyTreasure\DrawDateTaskFactory;
use App\GyTreasure\DrawIssueTaskFactory;
use App\GyTreasure\DrawStartIssuesTaskFactory;
use GyTreasure\Drawer;

class IssueDrawerTaskFactory
{
    /**
     * @param  int  $lotteryId
     * @return \GyTreasure\Tasks\DrawDateTask|null
     */
    public function makeDrawDateTask($lotteryId)
    {
        return app()->make(DrawDateTaskFactory::class)->make($lotteryId);
    }

    /**
     * @param  int  $lotteryId
     * @return \GyTreasure\Tasks\DrawStartIssuesTask|null
     */
    public function makeDrawStartIssuesTask($lotteryId)
    {
        return app()->make(DrawStartIssuesTaskFactory::class)->make($lotteryId);
    }

    /**
     * @param  int  $lotteryId
     * @return \GyTreasure\Tasks\DrawIssueTask|null
     */
    public function makeDrawIssueTask($lotteryId)
    {
        return app()->make(DrawIssueTaskFactory::class)->make($lotteryId);
    }

    /**
     * @return \GyTreasure\Drawer
     */
    public function makeDrawer()
    {
        return new Drawer();
    }
}
