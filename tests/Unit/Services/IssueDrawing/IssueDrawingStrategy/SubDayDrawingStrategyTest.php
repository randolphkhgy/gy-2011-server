<?php

namespace Tests\Unit\Services\IssueDrawing\IssueDrawingStrategy;

use App\Services\IssueDrawerTaskFactory;
use App\Services\IssueDrawing\IssueDrawingStrategy\IssueDrawingStrategy;
use App\Services\IssueDrawing\IssueDrawingStrategy\SubDayDrawingStrategy;
use App\Services\IssueGeneratorService;
use Carbon\Carbon;
use Mockery;
use Tests\TestCase;

class SubDayDrawingStrategyTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface|\App\Services\IssueGeneratorService
     */
    protected $generatorMock;

    /**
     * @var \Mockery\MockInterface|\App\Services\IssueDrawerTaskFactory
     */
    protected $taskFactoryMock;

    /**
     * @var \Mockery\MockInterface|\App\Services\IssueDrawing\IssueDrawingStrategy\IssueDrawingStrategy
     */
    protected $normalStrategyMock;

    /**
     * @var \App\Services\IssueDrawing\IssueDrawingStrategy\SubDayDrawingStrategy
     */
    protected $strategy;

    protected function setUp()
    {
        parent::setUp();

        $this->generatorMock        = Mockery::mock(IssueGeneratorService::class);
        $this->taskFactoryMock      = Mockery::mock(IssueDrawerTaskFactory::class);

        $this->normalStrategyMock   = Mockery::mock(IssueDrawingStrategy::class);

        $this->normalStrategyMock
            ->shouldReceive('generator')
            ->andReturn($this->generatorMock);

        $this->normalStrategyMock
            ->shouldReceive('taskFactory')
            ->andReturn($this->taskFactoryMock);

        $this->strategy             = new SubDayDrawingStrategy($this->normalStrategyMock);
    }

    protected function tearDown()
    {
        Mockery::close();

        parent::tearDown();
    }

    /**
     * 无第一个期号的结果.
     * 一般情况下不会出现此种情况
     */
    public function testDrawInvalid()
    {
        $lotteryId   = 1;
        $date        = Carbon::parse('+1 hour');
        $startNumber = 45;

        $this->generatorMock
            ->shouldReceive('firstEarliestWriteTime')
            ->once()
            ->with($lotteryId, $date)
            ->andReturnNull();

        $returnValue = $this->strategy->draw($lotteryId, $date, $startNumber);

        $this->assertNull($returnValue);
        $this->assertSame([], $this->strategy->issues());
    }

    /**
     * 抓取无法执行第一期的状况.
     */
    public function testDrawFuture()
    {
        $lotteryId   = 1;
        $date        = Carbon::parse('+1 hour');
        $startNumber = 45;

        $yesterday   = $date->copy()->subDay();

        $yesterdayDrawn = [
            ['issue' => '20171004-002', 'winningNumbers' => ['4', '7', '3', '3', '3']],
            ['issue' => '20171004-001', 'winningNumbers' => ['5', '3', '1', '2', '1']],
        ];

        $yesterdayIssues = [
            ['issue' => '20171004-001'],
            ['issue' => '20171004-002'],
        ];

        $todayIssues = [
            ['issue' => '20171005-003'],
            ['issue' => '20171005-004'],
        ];

        $this->generatorMock
            ->shouldReceive('firstEarliestWriteTime')
            ->once()
            ->with($lotteryId, $date)
            ->andReturn($date);

        $this->normalStrategyMock
            ->shouldReceive('draw')
            ->once()
            ->with($lotteryId, Mockery::on(function ($date) use ($yesterday) {
                return ($date instanceof Carbon) && $yesterday->eq($date);
            }))
            ->andReturn($yesterdayDrawn);

        $this->generatorMock
            ->shouldReceive('getNumberFromIssue')
            ->once()
            ->with('20171004-002', $lotteryId)
            ->andReturn(2);

        $this->generatorMock
            ->shouldReceive('generate')
            ->once()
            ->with($lotteryId, $date, 3)
            ->andReturnUsing(function () use ($todayIssues) {
                foreach ($todayIssues as $row) {
                    yield $row;
                }
            });

        $this->normalStrategyMock
            ->shouldReceive('issues')
            ->once()
            ->withNoArgs()
            ->andReturn($yesterdayIssues);

        $returnArray = $this->strategy->draw($lotteryId, $date, $startNumber);

        $this->assertEquals($yesterdayDrawn, $returnArray);
        $this->assertSame(array_merge($yesterdayIssues, $todayIssues), $this->strategy->issues());
    }

    /**
     * 抓取无法执行第一期，并抓取前一天的资料也失败的状况.
     */
    public function testDrawFutureAndFail()
    {
        $lotteryId   = 1;
        $date        = Carbon::parse('+1 hour');
        $startNumber = 45;

        $yesterday   = $date->copy()->subDay();

        $this->generatorMock
            ->shouldReceive('firstEarliestWriteTime')
            ->once()
            ->with($lotteryId, $date)
            ->andReturn($date);

        $this->normalStrategyMock
            ->shouldReceive('draw')
            ->once()
            ->with($lotteryId, Mockery::on(function ($date) use ($yesterday) {
                return ($date instanceof Carbon) && $yesterday->eq($date);
            }))
            ->andReturnNull();

        $returnArray = $this->strategy->draw($lotteryId, $date, $startNumber);

        $this->assertNull($returnArray);
        $this->assertSame([], $this->strategy->issues());
    }

    /**
     * 抓取可执行第一期的状况.
     */
    public function testDrawPast()
    {
        $lotteryId   = 1;
        $date        = Carbon::yesterday();
        $startNumber = 45;

        $drawn = [
            ['issue' => '20171004-002', 'winningNumbers' => ['4', '7', '3', '3', '3']],
            ['issue' => '20171004-001', 'winningNumbers' => ['5', '3', '1', '2', '1']],
        ];

        $issues = [
            ['issue' => '20171004-001'],
            ['issue' => '20171004-002'],
        ];

        $this->generatorMock
            ->shouldReceive('firstEarliestWriteTime')
            ->once()
            ->with($lotteryId, $date)
            ->andReturn($date);

        $this->normalStrategyMock
            ->shouldReceive('draw')
            ->once()
            ->with($lotteryId, $date)
            ->andReturn($drawn);

        $this->normalStrategyMock
            ->shouldReceive('issues')
            ->once()
            ->withNoArgs()
            ->andReturn($issues);

        $returnArray = $this->strategy->draw($lotteryId, $date, $startNumber);

        $this->assertEquals($drawn, $returnArray);
        $this->assertSame($issues, $this->strategy->issues());
    }
}
