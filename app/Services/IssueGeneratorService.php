<?php

namespace App\Services;

use App\Services\IssueGenerator\IssueGeneratorServiceTime;
use App\Repositories\LotteryRepository;
use App\Services\IssueGenerator\IssueRules;
use App\Exceptions\LotteryNotFoundException;

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
        if (! $lottery) {
            throw new LotteryNotFoundException('Lottery is not found. (lotteryid=' . $lotteryid . ')');
        }

        $issuerule = new IssueRules($lottery->issuerule, $lottery->issueset);

        header('Content-Type: text/plain; charset=utf-8');
        $n = 0;
        while ($issue = $issuerule->newNumber()) {
            if ($n++ > 300) {
                break;
            }
            echo $issuerule->getDateTime(), "\t", $issue, "\r\n";
        }
        exit;

    }
}
