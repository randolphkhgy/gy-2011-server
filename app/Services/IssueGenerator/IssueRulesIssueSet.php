<?php

namespace App\Services\IssueGenerator;

use Carbon\Carbon;
use Illuminate\Support\Arr;

/**
 * 彩种设定.
 */
trait IssueRulesIssueSet
{
    /**
     * 彩种设定组
     *
     * @var array
     */
    protected $issueset;

    /**
     * 启用的彩种设定
     *
     * @var int
     */
    protected $activedIssueSetIndex = 0;

    /**
     * 初始化彩种设定组
     *
     * @param  array  $issueset
     * @return $this
     */
    protected function initIssueSet($issueset)
    {
        // 只需要已启用的项目 (status => 1)
        $this->issueset = array_values(array_filter($issueset, function ($row) {
            return $row['status'];
        }));

        // 彩种排序
        usort($this->issueset, function ($left, $right) {
            if ($left['sort'] < $right['sort']) {
                return -1;
            } elseif ($left['sort'] > $right['sort']) {
                return 1;
            }
            return 0;
        });

        // 启用的彩种设定
        $this->activedIssueSetIndex = 0;

        // 设定初始时间
        $this->setUpTime();

        return $this;
    }

    /**
     * 设定初始时间
     *
     * @return $this
     */
    protected function setUpTime()
    {
        static::_setTime($this->date, $this->getIssueSetting('starttime', '00:00:00'));
        return $this;
    }

    /**
     * 取得彩种设定组
     *
     * @return array
     */
    public function getIssueSet()
    {
        return $this->issueset;
    }

    /**
     * 取得已启用的彩种设定
     *
     * @return array
     */
    public function acitvedIssueSet()
    {
        return Arr::get($this->issueset, $this->activedIssueSetIndex);
    }

    /**
     * 是否有已启用的彩种设定
     *
     * @return bool
     */
    public function hasAcitvedIssueSet()
    {
        return isset($this->issueset[$this->activedIssueSetIndex]);
    }

    /**
     * 下一个彩种设定
     *
     * @return $this
     */
    public function nextIssueSet()
    {
        $this->activedIssueSetIndex++;
        $this->setUpTime();
        return $this;
    }

    /**
     * 重置已启用的彩种设定
     *
     * @return $this
     */
    public function resetActivedIssueSet()
    {
        $this->activedIssueSetIndex = 0;
        $this->setUpTime();
        return $this;
    }

    /**
     * 取得目前已启用的彩种设定的设定值
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public function getIssueSetting($key, $default = null)
    {
        return Arr::get($this->acitvedIssueSet(), $key, $default);
    }

    /**
     * 移动至下一个时间
     *
     * @return bool
     *
     * @throws \Exception
     */
    protected function nextTime()
    {
        if (! $this->hasAcitvedIssueSet()) {
            // 已经没有可使用的彩种设定, 跳出函式
            return false;
        }

        $cycle = $this->getIssueSetting('cycle', 0);
        if (! $cycle) {
            // 如果 cycle 没数值会造成无限回圈
            throw new Exception("Found no 'cycle' or zero, it would cause infinite loop.");
        }

        $starttime = static::_setTime($this->date->copy(), $this->getIssueSetting('starttime', '00:00:00'));
        $endtime   = static::_setTime($this->date->copy(), $this->getIssueSetting('endtime', '23:59:59'));

        ($endtime <= $starttime) && $endtime->addDay();

        $isFirst = $this->date->eq($starttime);
        if ($isFirst && ($firstendtime = $this->getIssueSetting('firstendtime'))) {
            $newdate = static::_setTime($this->date->copy(), $this->getIssueSetting('firstendtime', '00:00:00'));
        } else {
            $newdate = $this->date->copy()->modify('+' . $cycle . ' seconds');
        }

        if (! $newdate->between($starttime, $endtime)) {
            return $this->nextIssueSet()->nextTime();
        }

        $this->date = $newdate;

        return true;
    }

    /**
     * 使用设定字串设定时间
     *
     * @param  \Carbon\Carbon  $date
     * @param  string  $string
     * @return \Carbon\Carbon
     */
    private static function _setTime(Carbon $date, $string)
    {
        if (preg_match('/(\d+):(\d+):(\d+)/', $string, $match)) {
            list(, $hour, $minute, $second) = $match;
            $date->setTime($hour, $minute, $second);
        }
        return $date;
    }
}