<?php

namespace App\Services;

use App\Models\IssueInfo;
use App\Repositories\LotteryRepository;
use App\Exceptions\LotteryNotFoundException;
use GyTreasure\Issue\IssueGenerator\LegacyIssueRules\IssueGenerator;

class IssueGeneratorService
{
    /**
     * @var \App\Repositories\LotteryRepository
     */
    protected $lotteryRepo;

    /**
     * @var \App\Models\IssueInfo
     */
    protected $issueInfo;

    public function __construct(LotteryRepository $lotteryRepo, IssueInfo $issueInfo)
    {
        $this->lotteryRepo = $lotteryRepo;

        $this->issueInfo = $issueInfo;
    }

    public function generate($lotteryId)
    {
        $lottery = $this->lotteryRepo->find($lotteryId);
        if (! $lottery) {
            throw new LotteryNotFoundException('Lottery is not found. (lotteryId=' . $lotteryId . ')');
        }

        $generator = IssueGenerator::forge($lottery->issuerule, $lottery->issueset);

        $returnArray = array_map(function ($number) use ($lottery) {
            return $this->issueInfo->firstOrCreate([
                'lotteryid' => $lottery->lotteryid,
                'issue'     => $number['issue'],
            ], array_except($number, ['issue']));
        }, $generator->getArray());

        return $returnArray;
    }
}
