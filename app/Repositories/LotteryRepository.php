<?php

namespace App\Repositories;

use App\Criteria\ShuziCriteria;
use App\Models\Lottery;
use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Eloquent\BaseRepository;

class LotteryRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Lottery::class;
    }

    /**
     * 包含 ID
     *
     * @param  array  $idArr
     * @return $this
     */
    public function includes($idArr = [])
    {
         $this->model->orWhereIn('lotteryid', $idArr);
         return $this;
    }

    /**
     * 中国彩种 (含中国用户可玩的越南彩种).
     *
     * @return $this
     */
    public function shuzi()
    {
        $this->pushCriteria(ShuziCriteria::class);
        return $this;
    }

    /**
     * 系统是否有 country 栏位.
     *
     * @return bool
     */
    public function countryColumnExists()
    {
        $model = ($this->model instanceof Builder) ? $this->model->getModel() : $this->model;
        return $model->countryColumnExists();
    }

    /**
     * 撷取指定国家
     *
     * @param  int  $country
     * @return $this
     */
    public function country($country, $inclusion = false)
    {
        if ($this->countryColumnExists()) {
            $this->model = $this->model->where('country', $country);
        }
        return $this;
    }

    /**
     * 彩种类型
     *
     * @param  int  $type
     * @return $this
     */
    public function type($type)
    {
        $this->model = $this->model->where('lotterytype', $type);
        return $this;
    }

    /**
     * 取有有效玩法的彩种
     *
     * @return $this
     */
    public function isMethodNotClosed()
    {
        $this->model = $this->model
            ->whereHas('methods', function ($query) {
                $query->where('isclose', 0);
            });
        return $this;
    }
}