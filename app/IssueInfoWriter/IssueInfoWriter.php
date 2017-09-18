<?php

namespace App\IssueInfoWriter;

use App\IssueInfoWriter\WritingStrategy\GenericIssueInfoStrategy;
use App\IssueInfoWriter\WritingStrategy\IssueInfoWriterStrategy;
use App\IssueInfoWriter\WritingStrategy\MySqlIssueInfoStrategy;
use App\IssueInfoWriter\WritingStrategy\PgSqlIssueInfoStrategy;
use App\Models\IssueInfo;

class IssueInfoWriter
{
    /**
     * @var \App\Models\IssueInfo
     */
    protected $model;

    /**
     * @var \App\IssueInfoWriter\WritingStrategy\IssueInfoWriterStrategy
     */
    protected $strategy;

    /**
     * IssueInfoWriter constructor.
     * @param \App\Models\IssueInfo $model
     */
    public function __construct(IssueInfo $model)
    {
        $this->model = $model;

        $this->initStrategy();
    }

    /**
     * @return $this
     */
    protected function initStrategy()
    {
        $conn = $this->model->getConnection();

        switch ($conn->getDriverName()) {
            case 'mysql':
                $this->setStrategy(new MySqlIssueInfoStrategy($this->model));
                break;
            case 'pgsql':
                $this->setStrategy(new PgSqlIssueInfoStrategy($this->model));
                break;
            default:
                $this->setStrategy(new GenericIssueInfoStrategy($this->model));
                break;
        }

        return $this;
    }

    /**
     * 写入资料.
     *
     * @param  array  $array
     * @return $this
     */
    public function write(array $array = [])
    {
        $this->getStrategy()->write($array);

        return $this;
    }

    /**
     * @return \App\IssueInfoWriter\WritingStrategy\IssueInfoWriterStrategy
     */
    public function getStrategy()
    {
        return $this->strategy;
    }

    /**
     * @param  \App\IssueInfoWriter\WritingStrategy\IssueInfoWriterStrategy  $strategy
     * @return $this
     */
    public function setStrategy(IssueInfoWriterStrategy $strategy)
    {
        $this->strategy = $strategy;
        return $this;
    }
}
