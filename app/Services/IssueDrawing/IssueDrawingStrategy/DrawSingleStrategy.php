<?php

namespace App\Services\IssueDrawing\IssueDrawingStrategy;

use Carbon\Carbon;

class DrawSingleStrategy extends GenerateIssuesStrategy
{
    /**
     * @var \App\Services\IssueDrawing\IssueDrawingStrategy\GenerateIssuesStrategy
     */
    protected $strategy;

    /**
     * DrawSingleStrategy constructor.
     * @param \App\Services\IssueDrawing\IssueDrawingStrategy\GenerateIssuesStrategy $strategy
     */
    public function __construct(GenerateIssuesStrategy $strategy)
    {
        parent::__construct($strategy->generator(), $strategy->taskFactory());

        $this->strategy = $strategy;
    }

    /**
     * @param  int    $lotteryId
     * @param  \Carbon\Carbon  $date
     * @param  array  $issues
     * @return array|null
     */
    protected function drawProcess($lotteryId, Carbon $date, array $issues)
    {
        if (count($issues) == 1) {
            /* 当只需抓一期期号时，改调用适用于抓一期的 API */

            $drawDateTask = $this->taskFactory->makeDrawIssueTask($lotteryId);

            if ($drawDateTask) {
                $issue          = head($issues);
                $winningNumbers = $drawDateTask->run($issue, $date);

                return ($winningNumbers) ? [compact('winningNumbers', 'issue')] : null;
            } else {
                return null;
            }
        }

        return $this->strategy->drawProcess($lotteryId, $date, $issues);
    }
}
