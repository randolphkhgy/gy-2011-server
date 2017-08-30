<?php

namespace App\Services;

use App\Exceptions\LotteryStartNumberRequiredException;
use App\GyTreasure\CodeFormatter;
use App\GyTreasure\GyTreasureIdentity;
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
                return empty($number['code']) && $number['earliestwritetime']->isPast();
            })
            ->pluck('issue')
            ->toArray();

        $drawDateTask = $this->drawerFactory->makeDrawDateTask($lotteryId);

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
        $drawStartIssuesTask = $this->drawerFactory->makeDrawStartIssuesTask($lotteryId);

        $data = $drawStartIssuesTask->run($date);

        if ($data) {
            $startNumber = $this->generator->getNumberFromIssue($data['first'], $lotteryId);

            $this->generator->generate($lotteryId, $date, $startNumber);

            return $data['issues'];
        }
        return null;
    }
}
