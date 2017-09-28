<?php

namespace App\Services\IssueDrawing\IssueDrawingStrategy;

use Carbon\Carbon;

class DrawDateStrategy extends GenerateIssuesStrategy
{
    /**
     * 官彩抓号.
     * 从已知开始流水号抓号.
     *
     * @param  int    $lotteryId
     * @param  \Carbon\Carbon  $date
     * @param  array  $issues
     * @return array|null
     */
    protected function drawProcess($lotteryId, Carbon $date, array $issues)
    {
        $drawDateTask = $this->drawerFactory->makeDrawDateTask($lotteryId);

        if ($issues && $drawDateTask) {
            return $drawDateTask->run($date, $issues);
        } else {
            return null;
        }
    }
}
