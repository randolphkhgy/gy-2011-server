<?php

namespace App\Services\IssueGenerator;

use Carbon\Carbon;
use Illuminate\Support\Arr;

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
     * 日期时间
     *
     * @var \Carbon\Carbon
     */
    protected $date;

    /**
     * IssueRules constructor.
     * @param string $issuerule
     * @param array  $issueset
     */
    public function __construct($issuerule, $issueset = [])
    {
        $this->date     = new Carbon;
        $this->issueset = $issueset;

        $this->initRules($issuerule);
        $this->initIssueSet($issueset);
    }

    /**
     * 初始化格式化规则
     *
     * @param  string  $issuerule
     * @return $this
     */
    protected function initRules($issuerule)
    {
        if (preg_match('/^(?<format>[^\|]*)(?:\|)(?<y>[01]),(?<m>[01]),(?<d>[01])?.*$/', $issuerule, $match)) {
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
     * @return \Carbon\Carbon
     */
    public function getDateTime()
    {
        return $this->date;
    }

    /**
     * 设置日期时间
     *
     * @param  \Carbon\Carbon  $date
     * @return $this
     */
    public function setDateTime(Carbon $date)
    {
        $this->date = $date;
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
     * @return void
     */
    protected function nextDay()
    {
        $this->date->addDay()->startOfDay();
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
     * @return string|null
     */
    public function newNumber()
    {
        if ($this->next()) {
            $number = $this->replaceYMD($this->format, $this->date);
            $number = $this->replaceNo($number, $this->number);

            return $number;
        } elseif ($this->nextDay()) {
            return $this->newNumber();
        }

        return null;
    }
}