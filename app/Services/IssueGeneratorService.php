<?php

namespace App\Services;

use App\Repositories\IssueInfoRepository;
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
     * @return array
     *
     * @throws \App\Exceptions\LotteryNotFoundException
     */
    public function generate($lotteryId)
    {
        $lottery = $this->lotteryRepo->find($lotteryId);
        if (! $lottery) {
            throw new LotteryNotFoundException('Lottery is not found. (lotteryId=' . $lotteryId . ')');
        }

        $generator = IssueGenerator::forge($lottery->issuerule, $lottery->issueset);

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
}
