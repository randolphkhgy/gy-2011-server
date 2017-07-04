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

    public function generate($lotteryId)
    {
        $lottery = $this->lotteryRepo->makeQuery()->find($lotteryId);
        if (! $lottery) {
            throw new LotteryNotFoundException('Lottery is not found. (lotteryId=' . $lotteryId . ')');
        }

        $issueRule = new IssueRules($lottery->issuerule, $lottery->issueset);

        header('Content-Type: text/plain; charset=utf-8');
        $n = 0;
        while ($issue = $issueRule->newNumber()) {
            if ($n++ > 300) {
                break;
            }
            echo $issueRule->getDateTime(), "\t", $issue, "\r\n";
        }
        exit;

    }
}
