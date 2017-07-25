<?php

namespace App\Services;

use App\GyTreasure\DrawDateTaskFactory;
use App\GyTreasure\DrawStartIssuesTaskFactory;
use GyTreasure\Drawer;

class IssueDrawerFactory
{
    /**
     * @var \App\GyTreasure\DrawDateTaskFactory
     */
    protected $drawDateTaskFactory;

    /**
     * @var \App\GyTreasure\DrawStartIssuesTaskFactory
     */
    protected $drawStartIssuesTaskFactory;

    /**
     * IssueDrawerService constructor.
     * @param \App\GyTreasure\DrawDateTaskFactory $drawDateTaskFactory
     * @param \App\GyTreasure\DrawStartIssuesTaskFactory $drawStartIssuesTaskFactory
     */
    public function __construct(
        DrawDateTaskFactory $drawDateTaskFactory,
        DrawStartIssuesTaskFactory $drawStartIssuesTaskFactory
    ) {
        $this->drawDateTaskFactory  = $drawDateTaskFactory;
        $this->drawStartIssuesTaskFactory   = $drawStartIssuesTaskFactory;
    }

    /**
     * @param  int  $lotteryId
     * @return \GyTreasure\Tasks\DrawDateTask|null
     */
    public function makeDrawDateTask($lotteryId)
    {
        return $this->drawDateTaskFactory->make($lotteryId);
    }

    /**
     * @param  int  $lotteryId
     * @return \GyTreasure\Tasks\DrawStartIssuesTask|null
     */
    public function makeDrawStartIssuesTask($lotteryId)
    {
        return $this->drawStartIssuesTaskFactory->make($lotteryId);
    }

    /**
     * @return \GyTreasure\Drawer
     */
    public function makeDrawer()
    {
        return new Drawer();
    }
}
