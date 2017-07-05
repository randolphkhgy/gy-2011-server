<?php

namespace App\Services\IssueGenerator;

use Carbon\Carbon;

/**
 * 奖期时间.
 */
class IssueDateTime
{
    /**
     * 奖期日期.
     *
     * @var \Carbon\Carbon
     */
    public $date;

    /**
     * 奖期时间
     *
     * @var \Carbon\Carbon
     */
    public $dateTime;

    /**
     * IssueDateTime constructor.
     *
     * @param \Carbon\Carbon $date
     */
    public function __construct(Carbon $date)
    {
        $this->date     = $date->copy()->startOfDay();
        $this->dateTime = $this->date->copy();
    }

    /**
     * 建立今天的奖期日期物件.
     *
     * @return static
     */
    public static function today()
    {
        return new static(Carbon::today());
    }

    /**
     * 下一天.
     *
     * @return static
     */
    public function nextDay()
    {
        return new static($this->date->copy()->addDay());
    }

    /**
     * 物件复制
     */
    public function __clone()
    {
        $this->date     = $this->date->copy();
        $this->dateTime = $this->dateTime->copy();
    }

    /**
     * 物件复制
     *
     * @return static
     */
    public function copy()
    {
        return clone $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->dateTime;
    }
}