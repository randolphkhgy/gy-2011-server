<?php

namespace App\Repositories;

use App\IssueInfoWriter\IssueInfoWriter;
use App\Models\IssueInfo;
use Carbon\Carbon;
use Prettus\Repository\Eloquent\BaseRepository;

class IssueInfoRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return IssueInfo::class;
    }

    /**
     * @param  int     $lotteryId
     * @param  string  $issue
     * @param  array   $attributes
     * @return bool
     */
    public function generate($lotteryId, $issue, array $attributes = [])
    {
        return $this->model->getQuery()->updateOrInsert([
            'lotteryid' => $lotteryId,
            'issue'     => $issue,
        ], $attributes);
    }

    /**
     * @param  int    $lotteryId
     * @param  array  $array
     * @return bool
     */
    public function generateInBatch($lotteryId, $array)
    {
        $data   = $this->combineIssues($lotteryId, $array);
        $this->setRowsDefaults($data);

        $writer = app()->make(IssueInfoWriter::class);
        $writer->write($data);
        return true;
    }

    /**
     * 彩种.
     *
     * @param  int  $lotteryId
     * @return $this
     */
    public function lottery($lotteryId)
    {
        $this->model = $this->model->where('lotteryid', $lotteryId);
        return $this;
    }

    /**
     * @param  \Carbon\Carbon  $date
     * @return $this
     */
    public function date(Carbon $date)
    {
        $this->model = $this->model->where('belongdate', $date->toDateString());
        return $this;
    }

    /**
     * @return $this
     */
    public function drawingNeeded()
    {
        $this->model = $this->model
            ->where('statusfetch', 0)
            ->where('earliestwritetime', '<=', Carbon::now());
        return $this;
    }

    /**
     * 期号 meta 资料, 不含状态资料及开奖号.
     * 例: 不含 code, statusfetch....
     *
     * @return array
     */
    public function issues()
    {
        return $this->all(['issue', 'belongdate', 'salestart', 'saleend', 'canneldeadline', 'earliestwritetime'])->toArray();
    }

    /**
     * 取得下一个抓取的期号.
     *
     * @return \App\Models\IssueInfo|null
     */
    public function nextDraw()
    {
        $this->model = $this->model
            ->where('statusfetch', 0)
            ->where('earliestwritetime', '>', Carbon::now())
            ->orderBy('earliestwritetime', 'asc')
            ->take(1);
        return $this->first();
    }

    /**
     * @param  array  $rows
     * @return array
     */
    protected function setRowsDefaults(array &$rows)
    {
        array_walk($rows, function (&$row) {
            isset($row['code'])        || ($row['code']        = '');
            isset($row['writetime'])   || ($row['writetime']   = null);
            isset($row['writeid'])     || ($row['writeid']     = 0);
            isset($row['statusfetch']) || ($row['statusfetch'] = 0);
            isset($row['statuscode'])  || ($row['statuscode']  = 0);
        });
        return $rows;
    }

    /**
     * 合并奖期栏位.
     *
     * @param  int    $lotteryId
     * @param  array  $array
     * @return array
     */
    protected function combineIssues($lotteryId, array $array)
    {
        $baseArray = ['lotteryid' => $lotteryId];

        return array_map(function ($row) use ($baseArray) {
            return $baseArray + $row;
        }, $array);
    }

    /**
     * @param  int  $limit
     * @return $this
     */
    public function needsDrawing($limit = 0)
    {
        $query = $this->model
            ->where('statusfetch', 0)
            ->where('earliestwritetime', '<=', Carbon::now())
            ->where('code', '');

        ($limit) && $query->limit($limit);

        $this->model = $query;

        return $this;
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->model->count();
    }
}
