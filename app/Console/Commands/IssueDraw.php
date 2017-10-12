<?php

namespace App\Console\Commands;

use App\Exceptions\LotteryNotFoundException;
use App\Services\IssueDrawerService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class IssueDraw extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'issue:draw {lotteryid} {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '抓取期号';

    /**
     * @var \App\Services\IssueDrawerService
     */
    protected $drawer;

    /**
     * Create a new command instance.
     *
     * @param  \App\Services\IssueDrawerService  $drawer
     */
    public function __construct(IssueDrawerService $drawer)
    {
        parent::__construct();

        $this->drawer = $drawer;
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
            $data       = $this->drawer->drawDate($lotteryId, $date);
            $time_end   = microtime(true);
            $time       = $time_end - $time_start;

            $this->info(sprintf('已抓取 %d 笔期号. (执行时间: %f 秒)', count($data), $time));
        } catch (LotteryNotFoundException $e) {
            $this->error('找不到指定的彩种');
        }
    }
}
