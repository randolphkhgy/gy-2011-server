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
     * @return \App\Repositories\LotteryRepository
     */
    protected function newQuery($basicColumns = false)
    {
        $this->lotteryRepo->makeQuery();

        // 撷取基本栏位与否
        ($basicColumns) && $this->lotteryRepo->basicColumns();

        return $this->lotteryRepo;
    }

    /**
     * 取得彩种
     *
     * @param  int  $lotteryid
     * @return \App\Models\Lottery
     */
    public function get($lotteryid, $basicColumns = false)
    {
        return $this->newQuery($basicColumns)->find($lotteryid);
    }

    /**
     * 所有彩种.
     *
     * @param  bool  $basicColumns  是否只撷取基本栏位
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allLotteries($basicColumns = false)
    {
        return $this->newQuery($basicColumns)->all();
    }

    /**
     * 时时彩 (中国).
     *
     * @param  bool  $basicColumns  是否只撷取基本栏位
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allShuzi($basicColumns = false)
    {
        // 新查询
        $this->newQuery($basicColumns);

        // 查询条件
        $this->lotteryRepo->shuzi()->type(0)->isMethodNotClosed();

        // 输出彩种列表
        return $this->lotteryRepo->all();
    }

    /**
     * 时时彩 (越南).
     *
     * @param  bool  $basicColumns  是否只撷取基本栏位
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allShuzivn($basicColumns = false)
    {
        // 新查询
        $this->newQuery($basicColumns);

        // 查询条件
        $this->lotteryRepo->type(0)->isMethodNotClosed();

        // 输出彩种列表
        return $this->lotteryRepo->all();
    }
}