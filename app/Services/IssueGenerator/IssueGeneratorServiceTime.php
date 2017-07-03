<?php

namespace App\Services\IssueGenerator;

use Carbon\Carbon;

trait IssueGeneratorServiceTime
{
    protected $starttime;
    protected $endtime;

    protected function initTime()
    {
        $this->starttime = (new Carbon)->startOfDay();
        $this->endtime   = (new Carbon)->endOfDay();
    }

    /**
     * 开始日期
     * 
     * @return string
     */
    public function getStartDate()
    {
        return $this->starttime->format('Y-m-d');
    }

    /**
     * 设置开始日期
     * 
     * @param  string  $startdate
     * @return $this
     */
    public function setStartDate($startdate)
    {
        $this->starttime = Carbon::parse($startdate)->startOfDay();
        return $this;
    }

    /**
     * 结束日期
     *
     * @return string
     */
    public function getEndDate()
    {
        return $this->endtime->format('Y-m-d');
    }

    /**
     * 设置结束日期
     *
     * @param  string  $enddate
     * @return $this
     */
    public function setEndDate($enddate)
    {
        $this->endtime = Carbon::parse($enddate)->startOfDay();
        return $this;
    }
}