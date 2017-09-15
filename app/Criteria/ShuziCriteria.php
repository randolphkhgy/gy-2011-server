<?php

namespace App\Criteria;

use Illuminate\Support\Facades\Config;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class ShuziCriteria
 * @package namespace App\Criteria;
 */
class ShuziCriteria implements CriteriaInterface
{
    /**
     * Apply criteria in query repository
     *
     * @param                     $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        if ($repository->countryColumnExists()) {
            $model = $model
                ->where(function ($query) use ($repository) {
                    // 取得可玩越南彩彩种
                    $inclusion = Config::get('lottery.shuzi.inclusion');

                    $query
                        ->whereIn('lotteryid', $inclusion)
                        ->orWhere('country', 1);
                });
        }

        return $model;
    }
}
