<?php

namespace App\GyTreasure;

use GyTreasure\Issue\GeneratorFactory;

class IssueGeneratorFactory
{
    public function make($lotteryId, array $config, $startNumber = 1)
    {
        $identity = GyTreasureIdentity::getIdentity($lotteryId);
        return GeneratorFactory::make('default', $identity, $config, $startNumber);
    }
}