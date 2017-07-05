<?php

namespace App\Services\IssueGenerator;

use Carbon\Carbon;

class IssueRules
{
    use IssueRulesIssueSet, IssueRulesFormattor;

    /**
     * 格式化字串
     *
     * @var string
     */
    public $format;

    /**
     * 流水号重置规则
     *
     * @var array
     */
    public $resetWhen = [
        'year'  => false,
        'month' => false,
        'day'   => false,
    ];

    /**
     * 流水号
     *
     * @var int
     */
    protected $number = 0;

    /**
     * 獎期时间
     *
     * @var \App\Services\IssueGenerator\IssueDateTime
     */
    protected $dateTime;

    /**
     * IssueRules constructor.
     * @param string $issueRule
     * @param array  $issueSet
     */
    public function __construct($issueRule, $issueSet = [])
    {
        $this->dateTime = IssueDateTime::today();
        $this->issueSet = $issueSet;

        $this->initRules($issueRule);
        $this->initIssueSet($issueSet);
    }

    /**
     * 初始化格式化规则
     *
     * @param  string  $issueRule
     * @return $this
     */
    protected function initRules($issueRule)
    {
        if (preg_match('/^(?<format>[^\|]*)(?:\|)(?<y>[01]),(?<m>[01]),(?<d>[01])?.*$/', $issueRule, $match)) {
            $this->format = $match['format'];
            $this->resetWhen['year']  = (bool) $match['y'];
            $this->resetWhen['month'] = (bool) $match['m'];
            $this->resetWhen['day']   = (bool) $match['d'];
        }
        // TODO 判断分析失败程序

        return $this;
    }

    /**
     * 取得目前的日期时间
     *
     * @return \App\Services\IssueGenerator\IssueDateTime
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * 设置日期时间
     *
     * @param  \App\Services\IssueGenerator\IssueDateTime  $date
     * @return $this
     */
    public function setDateTime(IssueDateTime $date)
    {
        $this->dateTime = $date;
        return $this;
    }

    /**
     * 下一个期号变动
     *
     * @return bool
     */
    protected function next()
    {
        if (! $this->nextTime()) {
            return false;
        }

        $this->nextNumber();

        return true;
    }

    /**
     * 移动至隔天
     *
     * @return bool
     */
    protected function nextDay()
    {
        $this->dateTime = $this->dateTime->nextDay();
        // TODO 未完成函式
        return false;
    }

    /**
     * 下一个流水号
     *
     * @return int
     */
    protected function nextNumber()
    {
        return ++$this->number;
    }

    /**
     * 产生新期号
     *
     * @return array|null
     */
    public function newNumber()
    {
        if ($this->next()) {
            $number = $this->replaceYMD($this->format, $this->dateTime->dateTime);
            $number = $this->replaceNo($number, $this->number);

            $issueDateTimeInfo = $this->issueSet->active()->issueDateTimeInfo($this->dateTime);

            return ['issue' => $number] + $issueDateTimeInfo;
        } elseif ($this->nextDay()) {
            return $this->newNumber();
        }

        return null;
    }
}