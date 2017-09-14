<?php

namespace App\Services;

use App\Repositories\LotteryRepository;

class LotteryService
{
    /**
     * @var \App\Repositories\LotteryRepository
     */
    protected $lotteryRepo;

    /**
     * LotteryService constructor.
     *
     * @param \App\Repositories\LotteryRepository $lotteryRepo
     */
    public function __construct(LotteryRepository $lotteryRepo)
    {
        $this->lotteryRepo = $lotteryRepo;
    }

    /**
     * 取得彩种
     *
     * @param  int  $lotteryid
     * @return \App\Models\Lottery
     */
    public function get($lotteryid, $basicColumns = false)
    {
        if ($basicColumns) {
            return $this->lotteryRepo->find($lotteryid, static::basicColumns());
        } else {
            return $this->lotteryRepo->find($lotteryid);
        }
    }

    /**
     * 所有彩种.
     *
     * @param  bool  $basicColumns  是否只撷取基本栏位
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allLotteries($basicColumns = false)
    {
        if ($basicColumns) {
            return $this->lotteryRepo->all(static::basicColumns());
        } else {
            return $this->lotteryRepo->all();
        }
    }

    /**
     * 时时彩 (中国).
     *
     * @param  bool  $basicColumns  是否只撷取基本栏位
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allShuzi($basicColumns = false)
    {
        $this->lotteryRepo->shuzi()->type(0)->isMethodNotClosed();

        if ($basicColumns) {
            return $this->lotteryRepo->all(static::basicColumns());
        } else {
            return $this->lotteryRepo->all();
        }
    }

    /**
     * 时时彩 (越南).
     *
     * @param  bool  $basicColumns  是否只撷取基本栏位
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allShuzivn($basicColumns = false)
    {
        $this->lotteryRepo->type(0)->isMethodNotClosed();

        if ($basicColumns) {
            return $this->lotteryRepo->all(static::basicColumns());
        } else {
            return $this->lotteryRepo->all();
        }
    }

    /**
     * 11选5
     *
     * @param  bool  $basicColumns  是否只撷取基本栏位
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allElevenFive($basicColumns = false)
    {
        $this->lotteryRepo->type(2)->isMethodNotClosed()->orderBy('sorts');

        if ($basicColumns) {
            return $this->lotteryRepo->all(static::basicColumns());
        } else {
            return $this->lotteryRepo->all();
        }
    }

    /**
     * 只撷取基本栏位
     *
     * @return array
     */
    protected static function basicColumns()
    {
        return [
            'lotteryid',
            'cnname'
        ];
    }
}