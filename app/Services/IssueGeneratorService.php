<?php

namespace App\Services;

use App\Services\IssueGenerator\IssueGeneratorServiceTime;
use App\Repositories\LotteryRepository;
use App\Services\IssueGenerator\IssueRules;

class IssueGeneratorService
{
    use IssueGeneratorServiceTime;

    protected $lotteryRepo;

    public function __construct(LotteryRepository $lotteryRepo)
    {
        $this->lotteryRepo = $lotteryRepo;

        $this->initTime();
    }

    public function generate($lotteryid)
    {
        $lottery = $this->lotteryRepo->makeQuery()->find($lotteryid);
        // TODO 找不到彩种程序

        $issuerule = new IssueRules($lottery->issuerule, $lottery->issueset);

        dd($issuerule->newNumber());

    }
}
