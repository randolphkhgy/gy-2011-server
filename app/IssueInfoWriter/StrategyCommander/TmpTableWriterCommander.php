<?php

namespace App\IssueInfoWriter\StrategyCommander;

use App\IssueInfoWriter\MassInsertionStrategy\MassInsertionStrategy;
use App\IssueInfoWriter\TmpIssueInfoTable;
use App\IssueInfoWriter\UpdatingStrategy\IssueInfoUpdatingStrategy;
use Illuminate\Database\Connection;

abstract class TmpTableWriterCommander extends IssueInfoWriterCommander
{
    /**
     * @var \App\IssueInfoWriter\MassInsertionStrategy\MassInsertionStrategy
     */
    protected $massInsertionStrategy;

    /**
     * @var \App\IssueInfoWriter\UpdatingStrategy\IssueInfoUpdatingStrategy
     */
    protected $updatingStrategy;

    /**
     * TmpTableWriterCommander constructor.
     * @param \Illuminate\Database\Connection $connection
     * @param string $table
     */
    public function __construct(Connection $connection, $table)
    {
        parent::__construct($connection, $table);

        $massInsertionStrategyClass = $this->defaultMassInsertionStrategy();
        $this->setMassInsertionStrategy(new $massInsertionStrategyClass($connection, $table));

        $updatingStrategy = $this->defaultUpdatingStrategy();
        $this->setUpdatingStrategy(new $updatingStrategy($connection, $table));
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

    /**
     * @return string
     */
    abstract protected function defaultMassInsertionStrategy();

    /**
     * @return string
     */
    abstract protected function defaultUpdatingStrategy();

    /**
     * 写入资料.
     *
     * @param  array  $array
     * @return $this
     */
    public function write(array $array = [])
    {
        $this->writeInTmpTable($array);

        return $this;
    }

    /**
     * 借由暂存资料表写入资料库.
     *
     * @param  array  $array
     * @return $this
     */
    protected function writeInTmpTable(array $array = [])
    {
        $conn     = $this->getConnection();
        $tmpTable = TmpIssueInfoTable::generate($conn);

        $this->getMassInsertionStrategy()->setTable($tmpTable->getTable())->write($array);

        $this->getUpdatingStrategy()->write($tmpTable);

        return $this;
    }
}
