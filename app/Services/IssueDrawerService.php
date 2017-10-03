<?php

namespace App\Services;

use App\GyTreasure\CodeFormatter;
use App\Services\IssueDrawing\IssueDrawnCombine;
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
        /* 抓取期号及抓号资料 */
        $info = $this->drawNumbers($lotteryId, $date);

        /* 合并期号及抓号资料 */
        $data = $this->combineResult($lotteryId, $info['drawn'], $info['issues']);

        /* 没有任何资料不需要储存结果 */
        if (! $data) {
            return [];
        }

        /* 储存结果 */
        return $this->generator->save($lotteryId, $data);
    }

    /**
     * 指定日期抓号.
     *
     * @param  int  $lotteryId
     * @param  \Carbon\Carbon $date
     * @return array
     *
     * @throws \App\Exceptions\LotteryNotFoundException
     */
    protected function drawNumbers($lotteryId, Carbon $date)
    {
        $drawer   = $this->drawerFactory->make($lotteryId);

        /* 取得抓号资料 */
        $drawn    = (array) $drawer->draw($lotteryId, $date);

        /* 取得期号资料 */
        $issues   = $drawer->issues();

        return compact('drawn', 'issues');
    }

    /**
     * 合并奖期及抓号资料.
     *
     * @param  int    $lotteryId
     * @param  array  $drawn
     * @param  array  $issues
     * @return array
     */
    protected function combineResult($lotteryId, array $drawn, array $issues)
    {
        $handler = IssueDrawnCombine::create()->setIssues($issues)->setDrawn($drawn);
        return $handler->combine(function ($issue, $drawn) use ($lotteryId) {
            $issue['code']        = CodeFormatter::format($lotteryId, $drawn['winningNumbers']);
            $issue['writetime']   = Carbon::now();
            $issue['writeid']     = 255;
            $issue['statusfetch'] = 2;
            $issue['statuscode']  = 2;
            return $issue;
        });
    }

}
