<?php

namespace App\Services;

use App\Exceptions\LotteryStartNumberRequiredException;
use App\GyTreasure\CodeFormatter;
use App\GyTreasure\DrawingGeneratorFactory;
use App\GyTreasure\GyTreasureIdentity;
use App\Repositories\IssueInfoRepository;
use Carbon\Carbon;
use GyTreasure\Issue\DrawingGenerator\DrawingStrategyFactory;

class IssueDrawerService
{
    /**
     * @var \App\Repositories\IssueInfoRepository
     */
    protected $issueInfoRepo;

    /**
     * @var \App\Services\IssueGeneratorService
     */
    protected $generator;

    /**
     * @var \App\GyTreasure\DrawDateTaskFactory
     */
    protected $drawDateTaskFactory;

    /**
     * @var \App\GyTreasure\DrawStartIssuesTaskFactory
     */
    protected $drawStartIssuesTaskFactory;

    /**
     * @var \App\Services\IssueDrawerFactory
     */
    protected $drawerFactory;

    /**
     * IssueDrawerService constructor.
     * @param \App\Repositories\IssueInfoRepository $issueInfoRepo
     * @param \App\Services\IssueGeneratorService $generator
     * @param \App\Services\IssueDrawerFactory $drawerFactory
     */
    public function __construct(
        IssueInfoRepository $issueInfoRepo,
        IssueGeneratorService $generator,
        IssueDrawerFactory $drawerFactory
    ) {
        $this->issueInfoRepo = $issueInfoRepo;
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
        $array = array_map(function ($draw, $issue) use ($lotteryId) {

            if ($draw) {
                $issue['code']        = CodeFormatter::format($lotteryId, $draw['winningNumbers']);
                $issue['writetime']   = Carbon::now();
                $issue['writeid']     = 255;
                $issue['statusfetch'] = 2;
                $issue['statuscode']  = 2;
            } else {
                $issue['code']        = '';
                $issue['writetime']   = null;
                $issue['writeid']     = 0;
                $issue['statusfetch'] = 0;
                $issue['statuscode']  = 0;
            }

            return $issue;
        }, $data, $issues);

        return $this->generator->save($lotteryId, $array);
    }

    /**
     * @return int
     */
    public function checkDrawing()
    {
        $drawer         = $this->drawerFactory->makeDrawer();
        $limit          = 5;
        $issueArray     = $this->issueInfoRepo->needsDrawing($limit)->all();
        $count          = 0;

        foreach ($issueArray as $row) {
            $identity       = GyTreasureIdentity::getIdentity($row['lotteryid']);
            $winningNumbers = $drawer->drawSingle($identity, $row['issue'], new Carbon($row['belongdate']));

            if ($winningNumbers) {
                $code = CodeFormatter::format($row['lotteryid'], $winningNumbers);
                $this->issueInfoRepo->writeCode($row['lotteryid'], $row['issue'], $code);

                $count++;
            }
        }

        return $count;
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
        if (DrawingGeneratorFactory::isAvailable($lotteryId)) {
            // 自主彩抓号
            $data = $this->selfDrawing($lotteryId, $date, $issues);
        } else {
            try {
                // 官彩抓号
                $data = $this->drawDateTask($lotteryId, $date, $issues);
            } catch (LotteryStartNumberRequiredException $e) {
                // 官彩抓号 (期号不是从 1 开始)
                $data = $this->drawStartIssuesTask($lotteryId, $date, $issues);
            }
        }
        return is_array($data) ? $data : [];
    }

    /**
     * 自主彩抓号.
     *
     * @param  int  $lotteryId
     * @param  \Carbon\Carbon  $date
     * @param  array  $issues
     * @return array
     */
    protected function selfDrawing($lotteryId, Carbon $date, &$issues = array())
    {
        $issues      = iterator_to_array($this->generator->generate($lotteryId, $date));
        $drawing     = $this->filterNeededDrawing($issues);
        $generator   = (new DrawingGeneratorFactory)->make($lotteryId);
        $allNumbers  = $generator->generate(count($drawing));

        return array_map(function ($issue, $winningNumbers) {
            return compact('winningNumbers', 'issue');
        }, $drawing, $allNumbers);
    }

    /**
     * 官彩抓号主程序.
     *
     * @param  int  $lotteryId
     * @param  \Carbon\Carbon  $date
     * @param  array  $issues
     * @return array|null
     */
    protected function drawDateTask($lotteryId, Carbon $date, &$issues = array())
    {
        $issues       = iterator_to_array($this->generator->generate($lotteryId, $date));
        $drawing      = $this->filterNeededDrawing($issues);
        $drawDateTask = $this->drawerFactory->makeDrawDateTask($lotteryId);

        if ($drawing && $drawDateTask) {
            return $drawDateTask->run($date, $drawing);
        } else {
            return null;
        }
    }

    /**
     * 官彩抓号次要程序, 用于期号流水编号不以 1 开始.
     *
     * @param  int  $lotteryId
     * @param  \Carbon\Carbon $date
     * @param  array  $issues
     * @return array|null
     */
    protected function drawStartIssuesTask($lotteryId, Carbon $date, &$issues = array())
    {
        $drawStartIssuesTask = $this->drawerFactory->makeDrawStartIssuesTask($lotteryId);

        $data = $drawStartIssuesTask->run($date);

        if ($data) {
            $startNumber = $this->generator->getNumberFromIssue($data['first'], $lotteryId);

            $issues = $this->generator->generate($lotteryId, $date, $startNumber);

            return $data['issues'];
        }
        return null;
    }

    /**
     * 产生指定日期的所有期号, 并回传需要抓号的期号.
     *
     * @param  array $array
     * @return array
     */
    protected function filterNeededDrawing(array $array)
    {
        return array_column(array_filter($array, function ($number) {
            return empty($number['code']) && $number['earliestwritetime']->isPast();
        }), 'issue');
    }
}
