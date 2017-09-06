<?php

namespace App\GyTreasure;

use GyTreasure\Issue\DrawingGenerator\DrawingGenerator;
use GyTreasure\Issue\DrawingGenerator\DrawingStrategyFactory;

class DrawingGeneratorFactory
{
    /**
     * @param  int $lotteryId
     * @return \GyTreasure\Issue\DrawingGenerator\DrawingGenerator
     */
    public function make($lotteryId)
    {
        $identity = GyTreasureIdentity::getIdentity($lotteryId);
        return DrawingGenerator::forge($identity);
    }

    /**
     * @param  int  $lotteryId
     * @return bool
     */
    public static function isAvailable($lotteryId)
    {
        $identity = GyTreasureIdentity::getIdentity($lotteryId);
        return DrawingStrategyFactory::isIdAvailable($identity);
    }
}
