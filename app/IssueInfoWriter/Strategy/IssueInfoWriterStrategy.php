<?php

namespace App\IssueInfoWriter\Strategy;

use App\Models\IssueInfo;

abstract class IssueInfoWriterStrategy
{
    /**
     * @var \App\Models\IssueInfo
     */
    protected $model;

    /**
     * MySqlIssueInfoStrategy constructor.
     * @param \App\Models\IssueInfo $model
     */
    public function __construct(IssueInfo $model)
    {
        $this->model = $model;
    }

    /**
     * 写入资料.
     *
     * @param  array  $array
     * @return $this
     */
    abstract public function write(array $array = []);
}