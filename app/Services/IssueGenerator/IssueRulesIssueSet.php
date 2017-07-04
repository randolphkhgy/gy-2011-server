<?php

namespace App\Services\IssueGenerator;

/**
 * 操作彩种设定.
 */
trait IssueRulesIssueSet
{
    /**
     * 彩种设定组
     *
     * @var \App\Services\IssueGenerator\IssueSetCollection
     */
    protected $issueSet;

    /**
     * 初始化彩种设定组
     *
     * @param  array  $issueSet
     * @return $this
     */
    protected function initIssueSet($issueSet)
    {
        $this->issueSet = IssueSetCollection::loadRaw($issueSet)->available()->sortMe();

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
        $active = $this->issueSet->active();
        ($active) && $active->applyFirstTime($this->date);
        return $this;
    }

    /**
     * 取得彩种设定组
     *
     * @return \App\Services\IssueGenerator\IssueSetCollection
     */
    public function getIssueSet()
    {
        return $this->issueSet;
    }

    /**
     * 下一个彩种设定
     *
     * @return $this
     */
    public function nextIssueSet()
    {
        $this->issueSet->next();
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
        $this->issueSet->reset();
        $this->setUpTime();
        return $this;
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
        if (! $this->issueSet->active()) {
            // 已经没有可使用的彩种设定, 跳出函式
            return false;
        }

        $date = $this->issueSet->active()->nextCycle($this->date);
        if ($date) {
            $this->date = $date;
            return true;
        } else {
            return $this->nextIssueSet()->nextTime();
        }
    }
}