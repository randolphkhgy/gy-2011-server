<?php

namespace App\IssueInfoWriter\Strategy;

class GenericIssueInfoStrategy extends IssueInfoWriterStrategy
{
    /**
     * @param  int    $lotteryId
     * @param  array  $array
     * @return $this
     */
    public function write($lotteryId, array $array = [])
    {
        foreach ($array as $row) {
            $this->model->generate($lotteryId, $row['issue'], $row);
        }
        return $this;
    }
}