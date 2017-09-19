<?php

namespace App\IssueInfoWriter;

use App\IssueInfoWriter\UpdatingStrategy\GenericIssueInfoUpdatingStrategy;
use App\IssueInfoWriter\UpdatingStrategy\IssueInfoUpdatingStrategy;
use App\IssueInfoWriter\MassInsertionStrategy\GenericIssueInfoStrategy;
use App\IssueInfoWriter\MassInsertionStrategy\MassInsertionStrategy;
use App\IssueInfoWriter\MassInsertionStrategy\MySqlIssueInfoStrategy;
use App\IssueInfoWriter\MassInsertionStrategy\PgSqlIssueInfoStrategy;
use App\IssueInfoWriter\UpdatingStrategy\MySqlUpdatingStrategy;
use App\Models\IssueInfo;

class IssueInfoWriter
{
    /**
     * @var \App\Models\IssueInfo
     */
    protected $model;

    /**
     * @var \App\IssueInfoWriter\MassInsertionStrategy\MassInsertionStrategy
     */
    protected $massInsertionStrategy;

    /**
     * @var \App\IssueInfoWriter\UpdatingStrategy\IssueInfoUpdatingStrategy
     */
    protected $updatingStrategy;

    /**
     * IssueInfoWriter constructor.
     * @param \App\Models\IssueInfo $model
     */
    public function __construct(IssueInfo $model)
    {
        $this->model = $model;

        $this->initMassInsertionStrategy()->initUpdatingStrategy();
    }

    /**
     * @return $this
     */
    protected function initMassInsertionStrategy()
    {
        $conn  = $this->model->getConnection();
        $table = $this->model->getTable();

        switch ($conn->getDriverName()) {
            case 'mysql':
                $this->setMassInsertionStrategy(new MySqlIssueInfoStrategy($conn, $table));
                break;
            case 'pgsql':
                $this->setMassInsertionStrategy(new PgSqlIssueInfoStrategy($conn, $table));
                break;
            default:
                $this->setMassInsertionStrategy(new GenericIssueInfoStrategy($conn, $table));
                break;
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function initUpdatingStrategy()
    {
        $conn  = $this->model->getConnection();

        switch ($conn->getDriverName()) {
            case 'mysql':
                $this->setUpdatingStrategy(new MySqlUpdatingStrategy($this->model));
                break;
            default:
                $this->setUpdatingStrategy(new GenericIssueInfoUpdatingStrategy($this->model));
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
        $conn     = $this->model->getConnection();
        $tmpTable = TmpIssueInfoTable::generate($conn);

        $this->getMassInsertionStrategy()->setTable($tmpTable->getTable())->write($array);

        $this->getUpdatingStrategy()->write($tmpTable);

        return $this;
    }

    /**
     * @return \App\IssueInfoWriter\MassInsertionStrategy\MassInsertionStrategy
     */
    public function getMassInsertionStrategy()
    {
        return $this->massInsertionStrategy;
    }

    /**
     * @param  \App\IssueInfoWriter\MassInsertionStrategy\MassInsertionStrategy  $strategy
     * @return $this
     */
    public function setMassInsertionStrategy(MassInsertionStrategy $strategy)
    {
        $this->massInsertionStrategy = $strategy;
        return $this;
    }

    /**
     * @return \App\IssueInfoWriter\UpdatingStrategy\IssueInfoUpdatingStrategy
     */
    public function getUpdatingStrategy()
    {
        return $this->updatingStrategy;
    }

    /**
     * @param  \App\IssueInfoWriter\UpdatingStrategy\IssueInfoUpdatingStrategy  $strategy
     * @return $this
     */
    public function setUpdatingStrategy(IssueInfoUpdatingStrategy $strategy)
    {
        $this->updatingStrategy = $strategy;
        return $this;
    }
}
