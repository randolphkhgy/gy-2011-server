<?php

namespace App\Repositories;

use App\Models\Lottery;

class LotteryRepository
{
    /**
     * @var \App\Models\Lottery
     */
    protected $model;

    /**
     * @var \Illuminate\Database\Eloquent\Builder
     */
    protected $query;

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
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all($columns = ['*'])
    {
        return $this->query->get();
    }

    /**
     * @param  int  $perPage
     * @param  array  $columns
     * @param  string  $pageName
     * @param  int|null  $page
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        return $this->query->paginate($perPage, $columns, $pageName, $page);
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
     * 撷取指定国家
     *
     * @param  int  $country
     * @return $this
     */
    public function country($country)
    {
        $this->query->where('country', $country);
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

    /**
     * 全新查询
     *
     * @return $this
     */
    public function makeQuery()
    {
        $this->query = $this->model->newQuery();
        return $this;
    }
}