<?php

namespace App\GyTreasure;

class GyTreasureIdentity
{
    /**
     * 取得 GyTreasure 函式库的彩种 ID.
     *
     * @param  int  $lotteryId
     * @return string|null
     */
    public static function getIdentity($lotteryId)
    {
        return config("gytreasure.identities.$lotteryId");
    }
}
