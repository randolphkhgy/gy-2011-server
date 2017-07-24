<?php

namespace App\Services;

use App\Exceptions\LotteryStartNumberRequiredException;
use App\GyTreasure\CodeFormatter;
use App\GyTreasure\DrawDateTaskFactory;
use App\GyTreasure\DrawStartIssuesTaskFactory;
use App\Repositories\IssueInfoRepository;
use Carbon\Carbon;

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
     * IssueDrawerService constructor.
     * @param \App\Repositories\IssueInfoRepository $issueInfoRepo
     * @param \App\Services\IssueGeneratorService $generator
     * @param \App\GyTreasure\DrawDateTaskFactory $drawDateTaskFactory
     * @param \App\GyTreasure\DrawStartIssuesTaskFactory $drawStartIssuesTaskFactory
     */
    public function __construct(
        IssueInfoRepository $issueInfoRepo,
        IssueGeneratorService $generator,
        DrawDateTaskFactory $drawDateTaskFactory,
        DrawStartIssuesTaskFactory $drawStartIssuesTaskFactory
    ) {
        $this->issueInfoRepo        = $issueInfoRepo;
        $this->generator            = $generator;
        $this->drawDateTaskFactory  = $drawDateTaskFactory;
        $this->drawStartIssuesTaskFactory   = $drawStartIssuesTaskFactory;
    }

    /**
     * @param  int  $lotteryId
     * @param  \Carbon\Carbon $date
     * @return array
     *
     * @throws \App\Exceptions\LotteryNotFoundException
     */
    public function drawDate($lotteryId, Carbon $date)
    {
        $data = $this->drawNumbers($lotteryId, $date);
        foreach ($data as $row) {
            $code = CodeFormatter::format($lotteryId, $row['winningNumbers']);
            $this->issueInfoRepo->writeCode($lotteryId, $row['issue'], $code);
        }

        return $data;
    }

    /**
     * @param  int  $lotteryId
     * @param  \Carbon\Carbon $date
     * @return array
     *
     * @throws \App\Exceptions\LotteryNotFoundException
     */
    protected function drawNumbers($lotteryId, Carbon $date)
    {
        try {
            $data = $this->drawDateTask($lotteryId, $date);
        } catch (LotteryStartNumberRequiredException $e) {
            $data = $this->drawStartIssuesTask($lotteryId, $date);
        }
        return is_array($data) ? $data : [];
    }

    /**
     * @param  int  $lotteryId
     * @param  \Carbon\Carbon  $date
     * @return array|null
     */
    protected function drawDateTask($lotteryId, Carbon $date)
    {
        $issues = collect($this->generator->generate($lotteryId, $date))
            ->filter(function ($number) {
                return ! $number['code'] && $number['earliestwritetime']->isPast();
            })
            ->pluck('issue')
            ->toArray();

        $drawDateTask = $this->drawDateTaskFactory->make($lotteryId);

        if ($issues && $drawDateTask) {

            return $drawDateTask->run($date, $issues);

        } else {

            return null;
        }
    }

    /**
     * @param  int  $lotteryId
     * @param  \Carbon\Carbon $date
     * @return array|null
     */
    protected function drawStartIssuesTask($lotteryId, Carbon $date)
    {
        $drawStartIssuesTask = $this->drawStartIssuesTaskFactory->make($lotteryId);

        $data = $drawStartIssuesTask->run($date);

        if ($data) {
            $startNumber = $data['first'];

            $this->generator->generate($lotteryId, $date, $startNumber);

            return $data['issues'];
        }
        return null;
    }
}
