<?php

namespace App\Services\IssueDrawing\IssueDrawingStrategy;

use App\GyTreasure\DrawingGeneratorFactory;
use Carbon\Carbon;

class SelfDrawingStrategy extends GenerateIssuesStrategy
{
    /**
     * 自主彩抓号.
     *
     * @param  int    $lotteryId
     * @param  \Carbon\Carbon  $date
     * @param  array  $issues
     * @return array|null
     */
    protected function drawProcess($lotteryId, Carbon $date, array $issues)
    {
        $generator   = (new DrawingGeneratorFactory())->make($lotteryId);
        $allNumbers  = $generator->generate(count($issues));

        return array_map(function ($issue, $winningNumbers) {
            return compact('winningNumbers', 'issue');
        }, $issues, $allNumbers);
    }
}
