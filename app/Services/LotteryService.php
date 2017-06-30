<?php

namespace App\Services;

use App\Repositories\LotteryRepository;
use App\Models\Lottery;

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
     * 所有彩种.
     *
     * @param  bool  $basicColumns  是否只撷取基本栏位
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allLotteries($basicColumns = false)
    {
        // 新查询
        $this->lotteryRepo->makeQuery();

        // 撷取基本栏位与否
        ($basicColumns) && $this->lotteryRepo->basicColumns();

        // 输出彩种列表
        return $this->lotteryRepo->all();
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
        $this->lotteryRepo->makeQuery();

        // 查询条件
        $this->lotteryRepo->country(Lottery::COUNTRY_CHINA)->isMethodNotClosed();

        // 撷取基本栏位与否
        ($basicColumns) && $this->lotteryRepo->basicColumns();

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
        $this->lotteryRepo->makeQuery();

        // 查询条件
        $this->lotteryRepo->isMethodNotClosed();

        // 撷取基本栏位与否
        ($basicColumns) && $this->lotteryRepo->basicColumns();

        // 输出彩种列表
        return $this->lotteryRepo->all();
    }
}