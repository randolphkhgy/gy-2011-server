<?php

namespace App\Services\IssueGenerator;

use Illuminate\Support\Collection;

class IssueSetCollection extends Collection
{
    /**
     * 启用的 index
     *
     * @var int
     */
    protected $activeIndex = 0;

    /**
     * IssueSetCollection constructor.
     * @param mixed $items
     */
    public function __construct($items = [])
    {
        parent::__construct($items);

        $this->items = array_values(array_filter($this->items, function ($item) {
            return $item instanceof IssueSet;
        }));
    }

    /**
     * 从资料载入集合
     *
     * @param  array  $raw
     * @return static
     */
    public static function loadRaw(array $raw)
    {
        $array = array_map(IssueSet::class . '::forge', $raw);
        return static::make($array);
    }

    /**
     * 依照 sort 值排序
     *
     * @return static
     */
    public function sortMe()
    {
        return $this->sort(function (IssueSet $left, IssueSet $right) {
            if ($left->get('sort') < $right->get('sort')) {
                return -1;
            } elseif ($left->get('sort') > $right->get('sort')) {
                return 1;
            }
            return 0;
        });
    }

    /**
     * 取得已启用的集合
     *
     * @return static
     */
    public function available()
    {
        return $this->filter(function (IssueSet $issueSet) {
            return $issueSet->isAvailable();
        })->values();
    }

    /**
     * 取得启用的项目
     *
     * @return \App\Services\IssueGenerator\IssueSet
     */
    public function active()
    {
        return $this->get($this->activeIndex);
    }

    /**
     * 取得下一个启用的项目
     *
     * @return \App\Services\IssueGenerator\IssueSet
     */
    public function next()
    {
        return $this->get(++$this->activeIndex);
    }

    /**
     * 重设启用的项目
     *
     * @return \App\Services\IssueGenerator\IssueSet
     */
    public function reset()
    {
        return $this->get($this->activeIndex = 0);
    }
}