<?php

namespace App\Repositories;

use App\Models\IssueInfo;
use Carbon\Carbon;

class IssueInfoDateRepository
{
    /**
     * @var \App\Models\IssueInfo
     */
    protected $model;

    /**
     * IssueInfoDateRepository constructor.
     * @param \App\Models\IssueInfo $model
     */
    public function __construct(IssueInfo $model)
    {
        $this->model = $model;
    }

    /**
     * @param  int  $maxDays
     * @return \Illuminate\Support\Collection
     */
    public function needDrawIssueGroup($maxDays)
    {
        return $this->model
            ->getQuery()
            ->groupBy('lotteryid', 'issue')
            ->distinct()
            ->select(['lotteryid', 'belongdate AS date'])
            ->where('statusfetch', 0)
            ->where('belongdate', '>', Carbon::parse(sprintf('-%d days', $maxDays))->toDateString())
            ->where('earliestwritetime', '<=', Carbon::now())
            ->get();
    }
}
