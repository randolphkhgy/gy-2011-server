<?php

namespace App\Services;

use App\GyTreasure\CodeFormatter;
use App\Services\IssueDrawing\SmartDateDrawerFactory;
use Carbon\Carbon;

class IssueDrawerService
{
    /**
     * @var \App\Services\IssueGeneratorService
     */
    protected $generator;

    /**
     * @var \App\Services\IssueDrawing\SmartDateDrawerFactory
     */
    protected $drawerFactory;

    /**
     * IssueDrawerService constructor.
     * @param \App\Services\IssueGeneratorService $generator
     * @param \App\Services\IssueDrawing\SmartDateDrawerFactory $drawerFactory
     */
    public function __construct(
        IssueGeneratorService $generator,
        SmartDateDrawerFactory $drawerFactory
    ) {
        $this->generator     = $generator;
        $this->drawerFactory = $drawerFactory;
    }

    /**
     * 指定日期抓号.
     *
     * @param  int  $lotteryId
     * @param  \Carbon\Carbon  $date
     * @return array
     *
     * @throws \App\Exceptions\LotteryNotFoundException
     */
    public function drawDate($lotteryId, Carbon $date)
    {
        $drawn = $this->drawNumbers($lotteryId, $date, $issues);
        $data  = $this->combineResult($lotteryId, $drawn, $issues);

        if (! $data) {
            // 抓不到资料.
            return [];
        }

        return $this->generator->save($lotteryId, $data);
    }

    /**
     * @param  array  $draws
     * @param  array  $issues
     * @return array
     */
    protected function filterAvailableDraws(array $draws, array $issues)
    {
        $availableIssues = array_column($issues, 'issue');
        return array_filter($draws, function ($row) use ($availableIssues) {
            return in_array($row['issue'], $availableIssues);
        });
    }

    /**
     * 指定日期抓号.
     * 不含写入.
     *
     * @param  int  $lotteryId
     * @param  \Carbon\Carbon $date
     * @param  array  $issues
     * @return array
     *
     * @throws \App\Exceptions\LotteryNotFoundException
     */
    protected function drawNumbers($lotteryId, Carbon $date, &$issues = array())
    {
        $drawer   = $this->drawerFactory->make($lotteryId);
        $result   = $drawer->draw($lotteryId, $date);
        $issues   = $drawer->issues();

        return is_array($result) ? $result : [];
    }

    /**
     * @param  array|null  $issues
     * @return array|null
     */
    protected function sortIssues($issues)
    {
        if (is_array($issues)) {
            usort($issues, function ($a, $b) {
                if ($a['issue'] == $b['issue']) {
                    return 0;
                }
                return ($a['issue'] < $b['issue']) ? -1 : 1;
            });
        }
        return $issues;
    }

    /**
     * @param  int         $lotteryId
     * @param  array|null  $drawn
     * @param  array       $issues
     * @return array
     */
    protected function combineResult($lotteryId, array $drawn, $issues)
    {
        $drawn  = $this->sortIssues($this->filterAvailableDraws($drawn, $issues));
        $issues = $this->sortIssues($issues);

        return array_map(function ($draw, $issue) use ($lotteryId) {

            if ($draw) {
                $issue['code'] = CodeFormatter::format($lotteryId, $draw['winningNumbers']);
                $issue['writetime'] = Carbon::now();
                $issue['writeid'] = 255;
                $issue['statusfetch'] = 2;
                $issue['statuscode'] = 2;
            }

            return $issue;
        }, $drawn, $issues);
    }
}
