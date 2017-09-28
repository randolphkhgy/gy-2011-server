<?php

namespace App\Services\IssueDrawing\IssueDrawingStrategy;

use Carbon\Carbon;

abstract class GenerateIssuesStrategy extends IssueDrawingStrategy
{
    /**
     * 抓号.
     * 先产生期号再抓号.
     *
     * @param  int       $lotteryId
     * @param  \Carbon\Carbon  $date
     * @param  int|null  $startNumber
     * @return array|null
     */
    public function draw($lotteryId, Carbon $date, $startNumber = null)
    {
        $issues         = $this->generateIssues($lotteryId, $date, $startNumber);
        $filteredIssues = $this->filterNeededDrawing($issues);

        return $this->drawProcess($lotteryId, $date, $filteredIssues);
    }

    /**
     * @param  int    $lotteryId
     * @param  \Carbon\Carbon  $date
     * @param  array  $issues
     * @return array|null
     */
    abstract protected function drawProcess($lotteryId, Carbon $date, array $issues);
}
