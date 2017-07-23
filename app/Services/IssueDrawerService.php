<?php

namespace App\Services;

use App\GyTreasure\CodeFormatter;
use App\GyTreasure\DrawDateTaskFactory;
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
     * IssueDrawerService constructor.
     * @param \App\Repositories\IssueInfoRepository $issueInfoRepo
     * @param \App\Services\IssueGeneratorService $generator
     * @param \App\GyTreasure\DrawDateTaskFactory $drawDateTaskFactory
     */
    public function __construct(
        IssueInfoRepository $issueInfoRepo,
        IssueGeneratorService $generator,
        DrawDateTaskFactory $drawDateTaskFactory
    ) {
        $this->issueInfoRepo        = $issueInfoRepo;
        $this->generator            = $generator;
        $this->drawDateTaskFactory  = $drawDateTaskFactory;
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
        $issues = collect($this->generator->generate($lotteryId, $date))
            ->filter(function ($number) {
                return ! $number['code'] && $number['earliestwritetime']->isPast();
            })
            ->pluck('issue')
            ->toArray();

        $drawDateTask = $this->drawDateTaskFactory->make($lotteryId);

        if ($issues && $drawDateTask) {

            $draw = $drawDateTask->run($date, $issues);

            return is_array($draw) ? $draw : [];

        } else {

            return [];
        }
    }
}
