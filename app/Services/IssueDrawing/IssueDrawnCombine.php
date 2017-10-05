<?php

namespace App\Services\IssueDrawing;

class IssueDrawnCombine
{
    /**
     * @var array
     */
    protected $issues = [];

    /**
     * @var array
     */
    protected $drawn = [];

    /**
     * IssueDrawnCombine constructor.
     * @param array $issues
     * @param array $drawn
     */
    public function __construct(array $issues = [], array $drawn = [])
    {
        $this->setIssues($issues)->setDrawn($drawn);
    }

    /**
     * @param  array  $issues
     * @param  array  $drawn
     * @return static
     */
    public static function create(array $issues = [], array $drawn = [])
    {
        return new static($issues, $drawn);
    }

    /**
     * 取得奖期.
     *
     * @return array
     */
    public function getIssues()
    {
        return $this->issues;
    }

    /**
     * 设定奖期.
     *
     * @param  array  $issues
     * @return $this
     */
    public function setIssues(array $issues)
    {
        $this->issues = $issues;
        return $this;
    }

    /**
     * 设定抓号资料.
     *
     * @param  array  $drawn
     * @return $this
     */
    public function setDrawn(array $drawn)
    {
        $this->drawn = $drawn;
        return $this;
    }

    /**
     * 过滤无期号的抓号资料.
     *
     * @param  array  $array
     * @return array
     */
    protected function filterDrawn(array $array)
    {
        $availableIssues = array_column($this->issues, 'issue');

        return array_filter($array, function ($drawn) use ($availableIssues) {
            return in_array($drawn['issue'], $availableIssues);
        });
    }

    /**
     * 建立抓号资料工厂.
     *
     * @return \Closure
     */
    protected function drawnFactory()
    {
        /* 过滤无期号资料的抓号资料 */
        $drawn           = $this->filterDrawn($this->drawn);

        /* 建立抓号资料索引 */
        $drawnIndexTable = array_flip(array_unique(array_column($drawn, 'issue')));

        return function ($issue) use ($drawn, $drawnIndexTable) {

            $index = isset($drawnIndexTable[$issue]) ? $drawnIndexTable[$issue] : false;
            return ($index !== false) ? $drawn[$index] : null;
        };
    }

    /**
     * 合并奖期和抓号资料.
     *
     * @param  callable  $combineFunc
     * @return array
     */
    public function combine(callable $combineFunc)
    {
        $fetchDrawn = $this->drawnFactory();

        return array_map(function ($issue) use ($fetchDrawn, $combineFunc) {

            if ($drawn = $fetchDrawn($issue['issue'])) {
                return call_user_func($combineFunc, $issue, $drawn);
            } else {
                return $issue;
            }

        }, $this->getIssues());
    }
}
