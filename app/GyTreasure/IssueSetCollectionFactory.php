<?php

namespace App\GyTreasure;

use GyTreasure\Issue\IssueGenerator\LegacyIssueRules\IssueSetCollection;
use GyTreasure\Issue\IssueInfoConfig;

class IssueSetCollectionFactory
{
    /**
     * @param  int  $lotteryId
     * @return \GyTreasure\Issue\IssueGenerator\LegacyIssueRules\IssueSetCollection|null
     */
    public function make($lotteryId)
    {
        $identity = GyTreasureIdentity::getIdentity($lotteryId);
        $config   = IssueInfoConfig::get($identity);
        return ($config && isset($config['issueset'])) ? IssueSetCollection::loadRaw($config['issueset']) : null;
    }
}
