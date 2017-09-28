<?php

namespace App\Services\IssueDrawing\IssueDrawingStrategy;

use App\Services\IssueDrawerFactory;
use App\Services\IssueGeneratorService;
use Carbon\Carbon;

abstract class IssueDrawingStrategy
{
    /**
     * @var array
     */
    protected $issues = [];

    /**
     * @var \App\Services\IssueGeneratorService
     */
    protected $generator;

    /**
     * @var \App\Services\IssueDrawerFactory
     */
    protected $drawerFactory;

    /**
     * IssueDrawingStrategy constructor.
     * @param  \App\Services\IssueGeneratorService  $generator
     * @param  \App\Services\IssueDrawerFactory     $drawerFactory
     */
    public function __construct(
        IssueGeneratorService $generator,
        IssueDrawerFactory $drawerFactory
    ) {
        $this->generator     = $generator;
        $this->drawerFactory = $drawerFactory;
    }

    /**
     * @param  int       $lotteryId
     * @param  \Carbon\Carbon  $date
     * @param  int|null  $startNumber
     * @return array|null
     */
    abstract public function draw($lotteryId, Carbon $date, $startNumber = null);

    /**
     * @param  int       $lotteryId
     * @param  \Carbon\Carbon  $date
     * @param  int|null  $startNumber
     * @return array
     */
    protected function generateIssues($lotteryId, Carbon $date, $startNumber = null)
    {
        $this->issues = iterator_to_array($this->generator->generate($lotteryId, $date, $startNumber));
        return $this->issues;
    }

    /**
     * @return array
     */
    public function issues()
    {
        return $this->issues;
    }

    /**
     * 产生指定日期的所有期号, 并回传需要抓号的期号.
     *
     * @param  array $array
     * @return array
     */
    protected function filterNeededDrawing(array $array)
    {
        return array_column(array_filter($array, function ($number) {
            return empty($number['code']) && $number['earliestwritetime']->isPast();
        }), 'issue');
    }
}
