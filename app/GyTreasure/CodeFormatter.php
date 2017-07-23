<?php

namespace App\GyTreasure;

use GyTreasure\Framework\Legacy\Issue\CodeFormatter as BaseCodeFormatter;

class CodeFormatter
{
    /**
     * @param  int  $lotteryId
     * @param  string  $code
     * @return array
     */
    public static function convert($lotteryId, $code)
    {
        $identity = static::identity($lotteryId);
        return ($identity === null)
            ? BaseCodeFormatter::genericConvert($code)
            : BaseCodeFormatter::convert($identity, $code);
    }

    /**
     * @param  int    $lotteryId
     * @param  array  $winningNumbers
     * @return string
     */
    public static function format($lotteryId, array $winningNumbers)
    {
        $identity = static::identity($lotteryId);
        return ($identity === null)
            ? BaseCodeFormatter::genericFormat($winningNumbers)
            : BaseCodeFormatter::format($identity, $winningNumbers);
    }

    /**
     * @param  int  $lotteryId
     * @return string|null
     */
    protected static function identity($lotteryId)
    {
        return config("gytreasure.identities.$lotteryId");
    }
}