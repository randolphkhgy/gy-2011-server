<?php

namespace App\Services\IssueDrawing;

use App\GyTreasure\DrawingGeneratorFactory;
use App\Services\IssueDrawerTaskFactory;
use App\Services\IssueDrawing\IssueDrawingStrategy\DrawDateStrategy;
use App\Services\IssueDrawing\IssueDrawingStrategy\DrawnResumeStrategy;
use App\Services\IssueDrawing\IssueDrawingStrategy\DrawSingleStrategy;
use App\Services\IssueDrawing\IssueDrawingStrategy\DrawStartIssuesStrategy;
use App\Services\IssueDrawing\IssueDrawingStrategy\SelfDrawingStrategy;
use App\Services\IssueDrawing\IssueDrawingStrategy\SubDayDrawingStrategy;
use App\Services\IssueGeneratorService;

class SmartDateDrawerFactory
{
    /**
     * @var \App\Services\IssueGeneratorService
     */
    protected $generator;

    /**
     * @var \App\Services\IssueDrawerTaskFactory
     */
    protected $taskFactory;

    /**
     * SmartDateDrawerFactory constructor.
     * @param \App\Services\IssueGeneratorService $generator
     * @param \App\Services\IssueDrawerTaskFactory $taskFactory
     */
    public function __construct(
        IssueGeneratorService $generator,
        IssueDrawerTaskFactory $taskFactory
    ) {
        $this->generator   = $generator;
        $this->taskFactory = $taskFactory;
    }

    /**
     * @param  int  $lotteryId
     * @return \App\Services\IssueDrawing\IssueDrawingStrategy\IssueDrawingStrategy
     */
    public function make($lotteryId)
    {
        if (DrawingGeneratorFactory::isAvailable($lotteryId)) {

            /*
             * 自主彩抓号
             */
            $resumeStrategy = $normalStrategy = new SelfDrawingStrategy($this->generator, $this->taskFactory);

        } elseif ($this->generator->startNumberRequired($lotteryId)) {

            /*
             * 官彩抓号 (流水号不是从 1 开始，並且無法得知開始流水号).
             *
             * 类别功用:
             * SubDayDrawingStrategy   - 必要時從前一天開始抓號
             * DrawStartIssuesStrategy - 先抓號判斷開始流水號
             */
            $normalStrategy = new SubDayDrawingStrategy(new DrawStartIssuesStrategy($this->generator, $this->taskFactory));

            /*
             * 官彩抓号 (补抓号码).
             *
             * DrawSingleStrategy      - 需要时改调用适用于只抓一期的 API
             * DrawDateStrategy        - 一般官彩抓号程序
             */
            $resumeStrategy = new DrawSingleStrategy(new DrawDateStrategy($this->generator, $this->taskFactory));

        } else {

            /*
             * 官彩抓号 (流水号从 1 开始)
             *
             * DrawSingleStrategy      - 需要时改调用适用于只抓一期的 API
             * DrawDateStrategy        - 一般官彩抓号程序
             */
            $resumeStrategy = $normalStrategy = new DrawSingleStrategy(new DrawDateStrategy($this->generator, $this->taskFactory));
        }

        /*
         * 资料库有资料时使用补抓号码程序，否则使用正常程序.
         */
        return new DrawnResumeStrategy($resumeStrategy, $normalStrategy);
    }
}
