<?php

namespace App\Services;

use App\Exceptions\LotteryStartNumberRequiredException;
use App\GyTreasure\IssueGeneratorFactory;
use App\Repositories\IssueInfoRepository;
use App\Repositories\LotteryRepository;
use App\Exceptions\LotteryNotFoundException;
use Carbon\Carbon;
use GyTreasure\Issue\GeneratorFactory;
use GyTreasure\Issue\IssueGenerator\LegacyIssueRules\IssueRules;
use GyTreasure\Issue\IssueGenerator\LegacyIssueRules\IssueSetCollection;

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
     * @param  int             $lotteryId
     * @param  \Carbon\Carbon  $date
     * @param  int|null        $startNumber
     * @return \Generator
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

        $config    = ['issuerule' => $lottery->issuerule, 'issueset' => $lottery->issueset];
        $generator = (new IssueGeneratorFactory)->make($lotteryId, $config, $startNumber);
        $generator->setDateRange($date, $date);

        return $generator->run();
    }

    /**
     * 是否需要开始流水号.
     *
     * @param  int  $lotteryId
     * @return bool
     *
     * @throws \App\Exceptions\LotteryNotFoundException
     */
    public function startNumberRequired($lotteryId)
    {
        $lottery = $this->lotteryRepo->find($lotteryId);
        if (! $lottery) {
            throw new LotteryNotFoundException('Lottery is not found. (lotteryId=' . $lotteryId . ')');
        }

        $rules = new IssueRules($lottery->issuerule);
        return $rules->isStartNumberNeeded();
    }

    /**
     * @param  int  $lotteryId
     * @param  \Generator|array  $data
     * @return array
     */
    public function save($lotteryId, $data)
    {
        ($data instanceof \Traversable) && ($data = iterator_to_array($data));
        $this->issueInfoRepo->generateInBatch($lotteryId, $data);
        return $data;
    }

    /**
     * @param  int             $lotteryId
     * @param  \Carbon\Carbon  $date
     * @param  int|null        $startNumber
     * @return array
     *
     * @throws \App\Exceptions\LotteryNotFoundException
     * @throws \App\Exceptions\LotteryStartNumberRequiredException
     */
    public function generateAndSave($lotteryId, Carbon $date, $startNumber = null)
    {
        return $this->save($lotteryId, $this->generate($lotteryId, $date, $startNumber));
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

    /**
     * @param  int  $lotteryId
     * @param  \Carbon\Carbon  $date
     * @return \Carbon\Carbon|null
     */
    public function firstEarliestWriteTime($lotteryId, Carbon $date)
    {
        $lottery = $this->lotteryRepo->find($lotteryId);
        if (! $lottery) {
            throw new LotteryNotFoundException('Lottery is not found. (lotteryId=' . $lotteryId . ')');
        }

        $issueSetCollection = IssueSetCollection::loadRaw($lottery->issueset);
        return $issueSetCollection->firstEarliestWriteTime($date);
    }

    /**
     * @param  int  $lotteryId
     * @param  \Carbon\Carbon  $date
     * @return array|null  回传需要抓号的期号.  若无任何需要抓号期号回传空阵列, 若资料库无任何期号则回传 null.
     */
    public function notDrawnIssues($lotteryId, Carbon $date)
    {
        $query = $this->issueInfoRepo->lottery($lotteryId)->date($date);
        if ($query->count()) {
            return $query->drawingNeeded()->issues();
        } else {
            return null;
        }
    }
}
