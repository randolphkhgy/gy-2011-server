<?php

namespace App\Services;

use App\Exceptions\LotteryStartNumberRequiredException;
use App\Repositories\IssueInfoRepository;
use App\Repositories\LotteryRepository;
use App\Exceptions\LotteryNotFoundException;
use Carbon\Carbon;
use GyTreasure\Issue\IssueGenerator\LegacyIssueRules\IssueGenerator;
use GyTreasure\Issue\IssueGenerator\LegacyIssueRules\IssueRules;

class IssueGeneratorService
{
    /**
     * @var \App\Repositories\LotteryRepository
     */
    protected $lotteryRepo;

    /**
     * @var \App\Repositories\IssueInfoRepository
     */
    protected $issueInfoRepo;

    /**
     * IssueGeneratorService constructor.
     * @param \App\Repositories\LotteryRepository $lotteryRepo
     * @param \App\Repositories\IssueInfoRepository $issueInfoRepo
     */
    public function __construct(
        LotteryRepository $lotteryRepo,
        IssueInfoRepository $issueInfoRepo
    ) {
        $this->lotteryRepo          = $lotteryRepo;
        $this->issueInfoRepo        = $issueInfoRepo;
    }

    /**
     * @param  int  $lotteryId
     * @param  \Carbon\Carbon  $date
     * @param  int|null  $startNumber
     * @return array
     *
     * @throws \App\Exceptions\LotteryNotFoundException
     * @throws \App\Exceptions\LotteryStartNumberRequiredException
     */
    public function generate($lotteryId, Carbon $date, $startNumber = null)
    {
        $lottery = $this->lotteryRepo->find($lotteryId);
        if (! $lottery) {
            throw new LotteryNotFoundException('Lottery is not found. (lotteryId=' . $lotteryId . ')');
        }

        if ($startNumber === null) {
            $startNumber = $this->startNumber($lottery);
        }

        $generator = IssueGenerator::forge($lottery->issuerule, $lottery->issueset, $startNumber);
        $generator->setDateRange($date, $date);

        $returnArray = array_map(function ($number) use ($lottery) {
            $model = $this->issueInfoRepo->firstOrNew([
                'lotteryid' => $lottery->lotteryid,
                'issue'     => $number['issue'],
            ]);
            $model->fill(array_except($number, ['issue']));
            $model->save();
            return $model;
        }, $generator->getArray());

        return $returnArray;
    }

    /**
     * 从期号分析流水号.
     *
     * @param  string  $issue
     * @param  int     $lotteryId
     * @return int|null
     */
    public function getNumberFromIssue($issue, $lotteryId)
    {
        $lottery = $this->lotteryRepo->find($lotteryId);
        if (! $lottery) {
            throw new LotteryNotFoundException('Lottery is not found. (lotteryId=' . $lotteryId . ')');
        }

        $rules = new IssueRules($lottery->issuerule);
        return $rules->getNumberFromIssue($issue);
    }

    /**
     * @param  \App\Models\Lottery  $lottery
     * @return int
     * @throws \App\Exceptions\LotteryStartNumberRequiredException
     */
    protected function startNumber($lottery)
    {
        $rules = new IssueRules($lottery->issuerule);
        if ($rules->isStartNumberNeeded()) {
            throw new LotteryStartNumberRequiredException("'startNumber' is required for generating the issues.");
        } else {
            return 1;
        }
    }
}
