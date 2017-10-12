<?php

namespace App\Console\Commands;

use App\Exceptions\LotteryNotFoundException;
use App\Services\IssueGeneratorService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class IssueGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'issue:generate {lotteryid} {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '产生期号';

    /**
     * @var \App\Services\IssueGeneratorService
     */
    protected $issueGenerator;

    /**
     * Create a new command instance.
     *
     * @param  \App\Services\IssueGeneratorService  $issueGenerator
     */
    public function __construct(IssueGeneratorService $issueGenerator)
    {
        parent::__construct();

        $this->issueGenerator = $issueGenerator;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $lotteryId = $this->argument('lotteryid');

        try {
            $date  = Carbon::parse($this->argument('date'));
        } catch (\Exception $e) {
            $this->error('无效的日期');
            return;
        }

        try {
            $time_start = microtime(true);
            $numbers    = $this->issueGenerator->generateAndSave($lotteryId, $date);
            $time_end   = microtime(true);
            $time       = $time_end - $time_start;

            $this->info(sprintf('已建立 %d 笔期号. (执行时间: %f 秒)', count($numbers), $time));
        } catch (LotteryNotFoundException $e) {
            $this->error('找不到指定的彩种');
        }
    }
}
