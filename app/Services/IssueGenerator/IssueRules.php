<?php

namespace App\Services\IssueGenerator;

use Carbon\Carbon;

class IssueRules
{
    /**
     * 格式化字串
     *
     * @var string
     */
    protected $format;

    /**
     * 流水号重置规则
     *
     * @var array
     */
    protected $resetWhen = [
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

    public function __construct($issuerule)
    {
        $this->date = new Carbon;

        $this->initRules($issuerule);
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
    protected function getDateTime()
    {
        return $this->date;
    }

    /**
     * 设置日期时间
     *
     * @param  \Carbon\Carbon  $date
     * @return $this
     */
    protected function setDateTime(Carbon $date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * 下一个期号变动
     *
     * @return $this
     */
    protected function next()
    {
        $this->nextNumber();

        return $this;
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
     * @return string
     */
    public function newNumber()
    {
        $this->next();

        $number = $this->replaceYMD($this->format, $this->date);
        $number = $this->replaceNo($number, $this->number);

        dd($number);

        return '';
    }

    /**
     * 取代 [Ymd] 字串.
     * 大小写不敏感
     *
     * @param  string  $format
     * @param  \Carbon\Carbon $date
     * @return string
     */
    protected function replaceYMD($format, Carbon $date)
    {
        return preg_replace_callback('/[ymd]+/i', function ($match) use ($date) {
            return $date->format($match[0]);
        }, $format);
    }

    /**
     * 取代 [n\d+] 字串.
     *
     * @param  string  $format
     * @param  int     $no
     * @return string
     */
    protected function replaceNo($format, $no)
    {
        return preg_replace_callback('/\[n(\d+)\]/', function ($match) use ($no) {
            return str_pad($no, $match[1], '0', STR_PAD_LEFT);
        }, $format);
    }
}