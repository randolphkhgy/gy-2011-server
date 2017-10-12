<?php

namespace App\Console\Commands;

use App\IssueInfoWriter\IssueInfoWriter;
use App\Models\IssueInfo;
use Carbon\Carbon;
use Illuminate\Console\Command;

class BulkIssues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'issue:bulktest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '建立大量期号进行写入速度测试';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->confirm('您确定要执行 5,000,000 笔期号写入速度测试?')) {
            $chunkSize = 2000;

            $writer = new IssueInfoWriter(app()->make(IssueInfo::class));

            $allTime = 0;

            for ($i = 0; $i < 5000000; $i += $chunkSize) {

                $all = array_map([$this, 'random'], range($i, $i + $chunkSize - 1));

                $time_start = microtime(true);
                $writer->write($all);
                $time_end = microtime(true);

                $execution_time = $time_end - $time_start;

                $allTime += $execution_time;
                $this->line($i . ': ' . $execution_time);
            }

            $this->info('--- All Time: ' . $allTime . ' ---');
        }
    }

    protected function random($i)
    {
        return [
            'lotteryid'         => 1,
            'code'              => sprintf('%05d', random_int(0, 99999)),
            'issue'             => $i,
            'belongdate'        => Carbon::now()->toDateString(),
            'salestart'         => Carbon::now(),
            'saleend'           => Carbon::now(),
            'canneldeadline'    => Carbon::now(),
            'earliestwritetime' => Carbon::now(),
            'writetime'         => Carbon::now(),
            'writeid'           => 255,
            'statusfetch'       => 2,
            'statuscode'        => 2,
        ];
    }
}
