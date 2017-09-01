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
    public function generateBatch($lotteryId, $array)
    {
        $data   = $this->combineIssues($lotteryId, $array);
        $writer = app()->make(IssueInfoWriter::class);
        $writer->write($data);
        return true;
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
     * @param  int     $lotteryId
     * @param  string  $issue
     * @param  string  $code
     * @return $this
     */
    public function writeCode($lotteryId, $issue, $code)
    {
        $this->model
            ->where('lotteryid', $lotteryId)
            ->where('issue', $issue)
            ->where('statusfetch', 0)
            ->update([
                'code'          => $code,
                'writetime'     => Carbon::now(),
                'writeid'       => 255,
                'statusfetch'   => 2,
                'statuscode'    => 2,
            ]);

        return $this;
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
}
