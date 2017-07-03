<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Config;
use App\Models\Lottery;

class LotteryRepository extends ModelRepository
{
    /**
     * LotteryRepository constrctor.
     *
     * @param \App\Models\Lottery $lottery
     */
    public function __construct(Lottery $lottery)
    {
        $this->model = $lottery;
        $this->makeQuery();
    }

    /**
     * 只撷取基本栏位
     *
     * @return $this
     */
    public function basicColumns()
    {
        $this->query->select([
            'lotteryid',
            'cnname'
        ]);
        return $this;
    }

    /**
     * 包含 ID
     *
     * @param  array  $idArr
     * @return $this
     */
    public function includes($idArr = [])
    {
         $this->query->orWhereIn('lotteryid', $idArr);
         return $this;
    }

    /**
     * 中国彩种 (含中国用户可玩的越南彩种).
     *
     * @return $this
     */
    public function shuzi()
    {
        $this->query
            ->where(function ($query) {
                // 取得可玩越南彩彩种
                $inclusion = Config::get('lottery.shuzi.inclusion');

                $this->query
                    ->whereIn('lotteryid', $inclusion)
                    ->orWhere('country', 1);
            });

        return $this;
    }

    /**
     * 撷取指定国家
     *
     * @param  int  $country
     * @return $this
     */
    public function country($country, $inclusion = false)
    {
        $this->query->where('country', $country);
        return $this;
    }

    /**
     * 彩种类型
     *
     * @param  int  $type
     * @return this
     */
    public function type($type)
    {
        $this->query->where('lotterytype', $type);
        return $this;
    }

    /**
     * 取有有效玩法的彩种
     *
     * @return $this
     */
    public function isMethodNotClosed()
    {
        $this->query
            ->whereHas('methods', function ($query) {
                $query->where('isclose', 0);
            });
        return $this;
    }
}