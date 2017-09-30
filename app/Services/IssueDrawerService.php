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
     * @param  \Carbon\Carbon $date
     * @return array
     *
     * @throws \App\Exceptions\LotteryNotFoundException
     */
    public function drawDate($lotteryId, Carbon $date)
    {
        $data  = $this->drawNumbers($lotteryId, $date, $issues);
        if (! $data) {
            // 抓不到资料.
            return [];
        }

        $array = array_map(function ($draw, $issue) use ($lotteryId) {

            if ($draw) {
                $issue['code']        = CodeFormatter::format($lotteryId, $draw['winningNumbers']);
                $issue['writetime']   = Carbon::now();
                $issue['writeid']     = 255;
                $issue['statusfetch'] = 2;
                $issue['statuscode']  = 2;
            }

            return $issue;
        }, $data, $issues);

        return $this->generator->save($lotteryId, $array);
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
}
