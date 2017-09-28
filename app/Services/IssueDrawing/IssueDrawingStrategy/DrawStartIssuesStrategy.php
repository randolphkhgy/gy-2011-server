<?php

namespace App\Services\IssueDrawing\IssueDrawingStrategy;

use Carbon\Carbon;

class DrawStartIssuesStrategy extends IssueDrawingStrategy
{
    /**
     * 官彩抓号.
     * 开始流水号未知.
     *
     * @param  int       $lotteryId
     * @param  \Carbon\Carbon  $date
     * @param  int|null  $startNumber
     * @return array|null
     */
    public function draw($lotteryId, Carbon $date, $startNumber = null)
    {
        $drawStartIssuesTask = $this->drawerFactory->makeDrawStartIssuesTask($lotteryId);

        $data = $drawStartIssuesTask->run($date);

        if ($data && ! is_null($startNumber = $this->generator->getNumberFromIssue($data['first'], $lotteryId))) {

            $this->generateIssues($lotteryId, $date, $startNumber);

            return $data['issues'];
        }

        $this->issues = [];
        return null;
    }
}
