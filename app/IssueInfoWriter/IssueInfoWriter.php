<?php

namespace App\IssueInfoWriter;

use App\IssueInfoWriter\Strategy\GenericIssueInfoStrategy;
use App\IssueInfoWriter\Strategy\IssueInfoWriterStrategy;
use App\IssueInfoWriter\Strategy\MySqlIssueInfoStrategy;
use App\Models\IssueInfo;

class IssueInfoWriter
{
    /**
     * @var \App\Models\IssueInfo
     */
    protected $model;

    /**
     * @var \App\IssueInfoWriter\Strategy\IssueInfoWriterStrategy
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
                // 若 database.php 有设定 PDO::MYSQL_ATTR_LOCAL_INFILE 可以大幅提高插入期号速度
                $mysqlAttrLocalInfile = $conn->getConfig('options.' . \PDO::MYSQL_ATTR_LOCAL_INFILE);
                $strategy = ($mysqlAttrLocalInfile) ? MySqlIssueInfoStrategy::class : GenericIssueInfoStrategy::class;
                break;
            default:
                $strategy = GenericIssueInfoStrategy::class;
        }

        $this->setStrategy(new $strategy($this->model));
        
        return $this;
    }

    /**
     * 写入资料.
     *
     * @param  int    $lotteryId
     * @param  array  $array
     * @return $this
     */
    public function write($lotteryId, array $array = [])
    {
        $this->getStrategy()->write($lotteryId, $array);

        return $this;
    }

    /**
     * @return \App\IssueInfoWriter\Strategy\IssueInfoWriterStrategy
     */
    public function getStrategy()
    {
        return $this->strategy;
    }

    /**
     * @param  \App\IssueInfoWriter\Strategy\IssueInfoWriterStrategy  $strategy
     * @return $this
     */
    public function setStrategy(IssueInfoWriterStrategy $strategy)
    {
        $this->strategy = $strategy;
        return $this;
    }
}
