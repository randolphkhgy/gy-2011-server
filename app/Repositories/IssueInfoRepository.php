<?php

namespace App\Repositories;

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
}
