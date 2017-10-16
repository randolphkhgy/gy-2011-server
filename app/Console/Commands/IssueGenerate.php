<?php

namespace App\Console\Commands;

use App\Exceptions\LotteryNotFoundException;
use App\Exceptions\LotteryStartNumberRequiredException;
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
    protected $signature = 'issue:generate {lotteryid} {date?} {--start=}';

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
     */
    public function handle()
    {
        $lotteryId      = $this->argument('lotteryid');
        $startNumber    = $this->optionStartNumber();

        try {
            $date  = Carbon::parse($this->argument('date'));
        } catch (\Exception $e) {
            $this->error('无效的日期');
            return;
        }

        try {
            $time_start = microtime(true);
            $numbers    = $this->issueGenerator->generateAndSave($lotteryId, $date, $startNumber);
            $time_end   = microtime(true);
            $time       = $time_end - $time_start;

            $this->info(sprintf('已建立 %d 笔期号. (执行时间: %f 秒)', count($numbers), $time));
        } catch (LotteryNotFoundException $e) {
            $this->error('找不到指定的彩种');
        } catch (LotteryStartNumberRequiredException $e) {
            $this->error('需要起始期号');
        }
    }

    /**
     * @return int|null
     */
    protected function optionStartNumber()
    {
        $startNumber = $this->option('start');
        return (is_string($startNumber) && ctype_digit($startNumber)) ? intval($startNumber) : null;
    }
}
