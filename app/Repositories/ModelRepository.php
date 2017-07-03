<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class ModelRepository
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
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
    public function __construct(Model $lottery)
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