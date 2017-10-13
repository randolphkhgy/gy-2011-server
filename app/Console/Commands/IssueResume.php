<?php

namespace App\Console\Commands;

use App\Services\IssueDrawerService;
use App\Services\IssueGeneratorService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class IssueResume extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'issue:resume {--watch}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '继续抓号指定/复源抓号程序';

    /**
     * @var \App\Services\IssueDrawerService
     */
    protected $drawer;

    /**
     * @var \App\Services\IssueGeneratorService
     */
    protected $generator;

    /**
     * Create a new command instance.
     *
     * @param  \App\Services\IssueDrawerService     $drawer
     * @param  \App\Services\IssueGeneratorService  $generator
     */
    public function __construct(IssueDrawerService $drawer, IssueGeneratorService $generator)
    {
        parent::__construct();

        $this->drawer    = $drawer;
        $this->generator = $generator;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $watch = $this->option('watch');

        do {
            $time_start = microtime(true);
            $result     = $this->resume();
            $time_end   = microtime(true);
            $time       = $time_end - $time_start;

            if ($result['group_count']) {
                $this->info(sprintf('完成抓号. (%d 个期号, %f 执行时间)', $result['count'], $time));
            } elseif (! $watch) {
                $this->info('不需要抓取任何期号.');
            }

            ($watch) && time_sleep_until($this->nextRunTime()->getTimestamp());
        } while($watch);
    }

    /**
     * @return array
     */
    protected function resume()
    {
        $group       = $this->generator->needDrawIssueGroup(2);
        $group_count = $group->count();

        if ($group_count) {

            $bar = $this->output->createProgressBar($group_count);
            $count = $group
                ->map(function ($row) use ($bar) {
                    try {
                        $count = count($this->drawer->drawDate($row->lotteryid, Carbon::parse($row->date)));
                    } catch (\Exception $e) {
                        $count = 0;
                    }
                    $bar->advance();
                    return $count;
                })
                ->sum();
            $bar->finish();

            // 避免后面的讯息和进度条黏在一起，插入一个换行字元
            $this->line('');

        } else {
            $count = 0;
        }

        return compact('group_count', 'count');
    }

    /**
     * 下一次执行时间.
     *
     * @return \Carbon\Carbon
     */
    protected function nextRunTime()
    {
        /* 等到下一次取号时间，或是最多一分钟后 */
        $nextTime   = array_get($this->generator->nextDraw(), 'earliestwritetime');
        $nextMinute = Carbon::parse('+1 minute');
        return ($nextTime) ? $nextMinute->min($nextTime) : $nextMinute;
    }
}
