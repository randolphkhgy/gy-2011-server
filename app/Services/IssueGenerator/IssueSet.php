<?php

namespace App\Services\IssueGenerator;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class IssueSet
{
    /**
     * 可用属性.
     * 可供外部读取的键值名称
     *
     * @var array
     */
    protected static $properties = [
        'starttime', 'endtime', 'firstendtime', 'cycle', 'endsale',
        'inputcodetime', 'droptime', 'status', 'sort'
    ];

    /**
     * 开始时间.
     *
     * @var array
     */
    protected $starttime;

    /**
     * 结束时间.
     *
     * @var array
     */
    protected $endtime;

    /**
     * 第一次周期结束时间.
     *
     * @var array
     */
    protected $firstendtime;

    /**
     * 周期 (秒).
     *
     * @var int
     */
    protected $cycle;

    /**
     * @var int
     */
    protected $endsale;

    /**
     * @var int
     */
    protected $inputcodetime;

    /**
     * @var int
     */
    protected $droptime;

    /**
     * @var bool
     */
    protected $status;

    /**
     * @var int
     */
    protected $sort;

    /**
     * IssueSet constructor.
     * @param array $setting
     *
     * @throws \Exception
     */
    public function __construct($setting)
    {
        $this->starttime     = $this->parseTime(Arr::get($setting, 'starttime'));
        $this->endtime       = $this->parseTime(Arr::get($setting, 'endtime'));
        $this->firstendtime  = $this->parseTime(Arr::get($setting, 'firstendtime'));

        $this->cycle         = (int) Arr::get($setting, 'cycle', 0);
        if (! $this->cycle) {
            // 如果 cycle 没数值会造成无限回圈
            throw new Exception("Found no 'cycle' or zero, it would cause infinite loop.");
        }

        $this->endsale       = (int) Arr::get($setting, 'endsale', 0);

        $this->inputcodetime = (int) Arr::get($setting, 'inputcodetime', 0);

        $this->droptime      = (int) Arr::get($setting, 'droptime', 0);

        $this->status        = (bool) Arr::get($setting, 'status', false);

        $this->sort          = (int) Arr::get($setting, 'sort', 0);
    }

    /**
     * 建立物件实体.
     *
     * @param  array  $setting
     * @return static
     */
    public static function forge($setting)
    {
        return new static($setting);
    }

    /**
     * 套用开始时间.
     *
     * @param  \Carbon\Carbon  $date
     * @return \Carbon\Carbon
     */
    public function applyFirstTime($date)
    {
        return $this->setTime($date, $this->starttime);
    }

    /**
     * 取得时间范围.
     *
     * @param  \Carbon\Carbon  $date
     * @return array
     */
    public function getRange(Carbon $date)
    {
        $starttime = $this->setTime($date->copy(), $this->starttime);
        $endtime   = $this->setTime($date->copy(), $this->endtime);

        if ($endtime <= $starttime) {
            // 若时间早于或等于开始时间，那么结束时间加上一天的时间
            $endtime->addDay();
        }

        return compact('starttime', 'endtime');
    }

    /**
     * 下一次周期的时间.
     *
     * @param  \App\Services\IssueGenerator\IssueDateTime  $dateTime
     * @return \App\Services\IssueGenerator\IssueDateTime|null
     */
    public function nextCycle(IssueDateTime $dateTime)
    {
        $range        = $this->getRange($dateTime->date);
        $isFirstCycle = $dateTime->dateTime->eq($range['starttime']);

        if ($isFirstCycle) {
            // 第一次不使用周期，直接设定时间
            $newDate  = $this->setTime($dateTime->dateTime->copy(), $this->firstendtime);
        } else {
            // 第二次以后使用周期设定时间
            $newDate  = $dateTime->dateTime->copy()->addSeconds($this->cycle);
        }

        // 是否在有效范围
        if ($newDate->between($range['starttime'], $range['endtime'])) {
            $newIssueDateTime = $dateTime->copy();
            $newIssueDateTime->dateTime = $newDate;
            return $newIssueDateTime;
        }
        return null;
    }

    /**
     * 转换时间格式.
     *
     * @param  string  $string
     * @return array
     */
    protected function parseTime($string)
    {
        if (preg_match('/^(\d{1,2}):(\d{1,2}):(\d{1,2})$/', $string, $match)) {
            return array_map('intval', array_slice($match, 1, 3));
        }
        return [0, 0, 0];
    }

    /**
     * 设定时间
     *
     * @param  \Carbon\Carbon  $date
     * @param  array   $time
     * @return \Carbon\Carbon
     */
    protected function setTime(Carbon $date, array $time)
    {
        return call_user_func_array([$date, 'setTime'], $time);
    }

    /**
     * 取得设定数值.
     *
     * @param  string  $key
     * @return mixed
     */
    public function get($key)
    {
        return in_array($key, static::$properties) ? $this->{$key} : null;
    }

    /**
     * 是否已启用.
     *
     * @return bool
     */
    public function isAvailable()
    {
        return $this->status;
    }

    /**
     * 奖期日期时间信息
     *
     * @param  \App\Services\IssueGenerator\IssueDateTime  $dateTime
     * @return array
     */
    public function issueDateTimeInfo(IssueDateTime $dateTime)
    {
        $range = $this->getRange($dateTime->date);

        $belongdate = $dateTime->date->format('Y-m-d');

        // 销售开始时间
        $salestart = $range['starttime']->copy()->subSeconds($this->endsale);

        // 销售结束时间
        $saleend = $dateTime->dateTime->copy()->subSeconds($this->endsale);

        // 撤单时间
        $canneldeadline = $dateTime->dateTime->copy()->subSeconds($this->droptime);

        // 最早录号时间
        $earliestwritetime = $dateTime->dateTime->copy()->addSeconds($this->inputcodetime);

        return compact('belongdate', 'salestart', 'saleend', 'canneldeadline', 'earliestwritetime');
    }
}