<?php

namespace App\IssueInfoWriter;

use App\IssueInfoWriter\StrategyCommander\GenericIssueInfoWriterCommander;
use App\IssueInfoWriter\StrategyCommander\IssueInfoWriterCommander;
use App\IssueInfoWriter\StrategyCommander\MySqlIssueInfoWriterCommander;
use App\IssueInfoWriter\StrategyCommander\PgSqlIssueInfoWriterCommander;
use App\Models\IssueInfo;

class IssueInfoWriter
{
    /**
     * @var \App\Models\IssueInfo
     */
    protected $model;

    /**
     * @var \App\IssueInfoWriter\StrategyCommander\IssueInfoWriterCommander
     */
    protected $commander;

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

        $this->initStrategyCommander();
    }

    /**
     * @return $this
     */
    protected function initStrategyCommander()
    {
        $conn  = $this->model->getConnection();
        $table = $this->model->getTable();

        switch ($conn->getDriverName()) {
            case 'mysql':
                $this->setCommander(new MySqlIssueInfoWriterCommander($conn, $table));
                break;
            case 'pgsql':
                $this->setCommander(new PgSqlIssueInfoWriterCommander($conn, $table));
                break;
            default:
                $this->setCommander(new GenericIssueInfoWriterCommander($conn, $table));
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
        $this->getCommander()->write($array);
        return $this;
    }

    /**
     * @return \App\IssueInfoWriter\StrategyCommander\IssueInfoWriterCommander
     */
    public function getCommander()
    {
        return $this->commander;
    }

    /**
     * @param  \App\IssueInfoWriter\StrategyCommander\IssueInfoWriterCommander  $commander
     * @return $this
     */
    public function setCommander(IssueInfoWriterCommander $commander)
    {
        $this->commander = $commander;
        return $this;
    }
}
