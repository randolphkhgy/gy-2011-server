<?php

namespace App\Services\IssueGenerator;

use Carbon\Carbon;

trait IssueGeneratorServiceTime
{
    protected $startTime;
    protected $endTime;

    protected function initTime()
    {
        $this->startTime = (new Carbon)->startOfDay();
        $this->endTime   = (new Carbon)->endOfDay();
    }

    /**
     * 开始日期
     *
     * @return string
     */
    public function getStartDate()
    {
        return $this->startTime->format('Y-m-d');
    }

    /**
     * 设置开始日期
     *
     * @param  string  $startDate
     * @return $this
     */
    public function setStartDate($startDate)
    {
        $this->startTime = Carbon::parse($startDate)->startOfDay();
        return $this;
    }

    /**
     * 结束日期
     *
     * @return string
     */
    public function getEndDate()
    {
        return $this->endTime->format('Y-m-d');
    }

    /**
     * 设置结束日期
     *
     * @param  string  $endDate
     * @return $this
     */
    public function setEndDate($endDate)
    {
        $this->endTime = Carbon::parse($endDate)->startOfDay();
        return $this;
    }
}