<?php

namespace App\Models;

use App\IssueInfoWriter\IssueInfoWriter;
use Illuminate\Database\Eloquent\Model;

class IssueInfo extends Model
{
    protected $table = 'issueinfo';

    protected $primaryKey = 'issueid';

    protected $fillable = [
        'lotteryid',
        'code',
        'issue',
        'belongdate',
        'salestart',
        'saleend',
        'canneldeadline',
        'earliestwritetime',
        'writetime',
        'writeid',
        'verifytime',
        'verifyid',
        'rank',
        'statusfetch',
        'statuscode',
        'statusdeduct',
        'statususerpoint',
        'statuscheckbonus',
        'statusbonus',
        'statustasktoproject',
        'statussynced',
        'statuslocks',
        'special_code',
        'special_status',
        'backup_status',
        'backupdel_status',
        'successtime1',
        'successtime2',
        'error',
    ];

    protected $dates = [
        'salestart',
        'saleend',
        'canneldeadline',
        'earliestwritetime',
        'writetime',
        'verifytime',
        'successtime1',
        'successtime2',
    ];

    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lottery()
    {
        return $this->belongsTo(Lottery::class, 'lotteryid', 'lotteryid');
    }

    /**
     * @param  int     $lotteryId
     * @param  string  $issue
     * @param  array   $attributes
     * @return bool
     */
    public function generate($lotteryId, $issue, array $attributes = [])
    {
        return $this->getQuery()->updateOrInsert([
            'lotteryid' => $lotteryId,
            'issue'     => $issue,
        ], $attributes);
    }

    /**
     * @param  int    $lotteryId
     * @param  array  $array
     * @return bool
     */
    public function generateBatch($lotteryId, array $array)
    {
        $writer = app()->make(IssueInfoWriter::class);
        $writer->write($lotteryId, $array);
        return true;
    }
}
