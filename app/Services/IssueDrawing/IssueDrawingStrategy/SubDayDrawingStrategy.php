<?php

namespace App\Services\IssueDrawing\IssueDrawingStrategy;

use Carbon\Carbon;

class SubDayDrawingStrategy extends IssueDrawingStrategy
{
    /**
     * @var \App\Services\IssueDrawing\IssueDrawingStrategy\IssueDrawingStrategy
     */
    protected $strategy;

    /**
     * SubDayDrawingStrategy constructor.
     * @param \App\Services\IssueDrawing\IssueDrawingStrategy\IssueDrawingStrategy $strategy
     */
    public function __construct(IssueDrawingStrategy $strategy)
    {
        parent::__construct($strategy->generator, $strategy->taskFactory);
        $this->strategy = $strategy;
    }

    /**
     * 抓号.
     * 当无法抓第一天奖期时从前一天开始抓取.
     *
     * @param  int       $lotteryId
     * @param  \Carbon\Carbon  $date
     * @param  int|null  $startNumber
     * @return array|null
     */
    public function draw($lotteryId, Carbon $date, $startNumber = null)
    {
        $firstEarliestWriteTime = $this->generator->firstEarliestWriteTime($lotteryId, $date);
        if (! $firstEarliestWriteTime) {

            $this->issues = [];
            return null;

        } elseif ($firstEarliestWriteTime->isFuture()) {

            // 第一期尚未开出，改开两天资料.

            $prevDay       = $date->copy()->subDay();
            $prevDayIssues = $this->strategy->draw($lotteryId, $prevDay);

            if ($prevDayIssues) {

                $lastIssue     = array_last($prevDayIssues);
                $startNumber   = $this->generator->getNumberFromIssue($lastIssue['issue'], $lotteryId) + 1;

                $oriDateIssues = iterator_to_array($this->generator->generate($lotteryId, $date, $startNumber));
                $this->issues  = array_merge($this->strategy->issues(), $oriDateIssues);

                return $prevDayIssues;
            }

            $this->issues = [];
            return null;

        } else {

            $result = $this->strategy->draw($lotteryId, $date);
            $this->issues = $this->strategy->issues();
            return $result;
        }
    }
}
