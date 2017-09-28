<?php

namespace App\Services;

use App\GyTreasure\CodeFormatter;
use App\GyTreasure\DrawingGeneratorFactory;
use App\Services\IssueDrawing\IssueDrawingStrategy\DrawDateStrategy;
use App\Services\IssueDrawing\IssueDrawingStrategy\DrawStartIssuesStrategy;
use App\Services\IssueDrawing\IssueDrawingStrategy\SelfDrawingStrategy;
use App\Services\IssueDrawing\IssueDrawingStrategy\SubDayDrawingStrategy;
use Carbon\Carbon;

class IssueDrawerService
{
    /**
     * @var \App\Services\IssueGeneratorService
     */
    protected $generator;

    /**
     * @var \App\Services\IssueDrawerFactory
     */
    protected $drawerFactory;

    /**
     * IssueDrawerService constructor.
     * @param \App\Services\IssueGeneratorService $generator
     * @param \App\Services\IssueDrawerFactory $issueDrawerFactory
     */
    public function __construct(
        IssueGeneratorService $generator,
        IssueDrawerFactory $issueDrawerFactory
    ) {
        $this->generator     = $generator;
        $this->drawerFactory = $issueDrawerFactory;
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
        $strategy = $this->generateStrategy($lotteryId);
        $result   = $strategy->draw($lotteryId, $date);
        $issues   = $strategy->issues();

        return is_array($result) ? $result : [];
    }

    /**
     * @param  int  $lotteryId
     * @return \App\Services\IssueDrawing\IssueDrawingStrategy\IssueDrawingStrategy
     */
    protected function generateStrategy($lotteryId)
    {
        if (DrawingGeneratorFactory::isAvailable($lotteryId)) {

            // 自主彩抓号
            return new SelfDrawingStrategy($this->generator, $this->drawerFactory);

        } elseif ($this->generator->startNumberRequired($lotteryId)) {

            // 官彩抓号 (流水号不是从 1 开始; 未知流水号)
            return new SubDayDrawingStrategy(new DrawStartIssuesStrategy($this->generator, $this->drawerFactory));

        } else {

            // 官彩抓号 (流水号从 1 开始)
            return new DrawDateStrategy($this->generator, $this->drawerFactory);
        }
    }
}
