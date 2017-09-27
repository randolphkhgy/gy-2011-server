<?php

namespace App\GyTreasure;

use GyTreasure\Issue\GeneratorFactory;

class IssueGeneratorFactory
{
    /**
     * @param  int    $lotteryId
     * @param  array  $config
     * @param  int    $startNumber
     * @return \GyTreasure\Issue\IssueGenerator\IssueGeneratorInterface|null
     */
    public function make($lotteryId, array $config, $startNumber = 1)
    {
        $identity = GyTreasureIdentity::getIdentity($lotteryId);
        return $identity ? GeneratorFactory::make('default', $identity, $config, $startNumber) : null;
    }
}