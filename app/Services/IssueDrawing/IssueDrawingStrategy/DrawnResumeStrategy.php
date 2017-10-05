<?php

namespace App\Services\IssueDrawing\IssueDrawingStrategy;

use Carbon\Carbon;

class DrawnResumeStrategy extends IssueDrawingStrategy
{
    /**
     * @var \App\Services\IssueDrawing\IssueDrawingStrategy\GenerateIssuesStrategy
     */
    protected $strategy;

    /**
     * @var \App\Services\IssueDrawing\IssueDrawingStrategy\IssueDrawingStrategy
     */
    protected $fallbackStrategy;

    /**
     * DrawnResumeStrategy constructor.
     * @param  \App\Services\IssueDrawing\IssueDrawingStrategy\GenerateIssuesStrategy     $strategy          资料库补号程序
     * @param  \App\Services\IssueDrawing\IssueDrawingStrategy\IssueDrawingStrategy|null  $fallbackStrategy  新期号创建程序
     */
    public function __construct(GenerateIssuesStrategy $strategy, IssueDrawingStrategy $fallbackStrategy = null)
    {
        parent::__construct($strategy->generator(), $strategy->taskFactory());
        $this->strategy             = $strategy;

        if ($fallbackStrategy === null) {
            $this->fallbackStrategy = $strategy;
        } else {
            $this->fallbackStrategy = $fallbackStrategy;
        }
    }

    /**
     * @param  int             $lotteryId
     * @param  \Carbon\Carbon  $date
     * @param  int|null        $startNumber
     * @return array|null
     */
    public function draw($lotteryId, Carbon $date, $startNumber = null)
    {
        $issues = $this->generator->notDrawnIssues($lotteryId, $date);

        if ($issues === null) {

            /*
             * 当天资料库无任何期号
             */

            $result       = $this->fallbackStrategy->draw($lotteryId, $date, $startNumber);
            $this->issues = $this->fallbackStrategy->issues();

        } elseif ($issues) {

            /*
             * 当天资料库有期号需要开号
             */

            $result       = $this->strategy->drawIssues($lotteryId, $date, $issues);
            $this->issues = $this->strategy->issues();

        } else {

            /*
             * 当天资料库有期号, 但无任何期号需要开号
             */

            $result       = [];
            $this->issues = [];
        }

        return $result;
    }
}
