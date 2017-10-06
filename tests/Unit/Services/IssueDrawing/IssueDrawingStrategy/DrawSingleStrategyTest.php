<?php

namespace Tests\Unit\Services\IssueDrawing\IssueDrawingStrategy;

use App\Services\IssueDrawerTaskFactory;
use App\Services\IssueDrawing\IssueDrawingStrategy\DrawSingleStrategy;
use App\Services\IssueDrawing\IssueDrawingStrategy\GenerateIssuesStrategy;
use App\Services\IssueGeneratorService;
use Carbon\Carbon;
use GyTreasure\Tasks\DrawIssueTask;
use Mockery;
use Tests\TestCase;

class DrawSingleStrategyTest extends TestCase
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
     * @var \PHPUnit_Framework_MockObject_MockObject|\App\Services\IssueDrawing\IssueDrawingStrategy\GenerateIssuesStrategy
     */
    protected $normalStrategyMock;

    /**
     * @var \Mockery\MockInterface|\GyTreasure\Tasks\DrawIssueTask
     */
    protected $taskMock;

    /**
     * @var \App\Services\IssueDrawing\IssueDrawingStrategy\DrawSingleStrategy
     */
    protected $strategy;

    protected function setUp()
    {
        parent::setUp();

        $this->generatorMock        = Mockery::mock(IssueGeneratorService::class);
        $this->taskFactoryMock      = Mockery::mock(IssueDrawerTaskFactory::class);
        $this->normalStrategyMock   = $this->getMockForAbstractClass(GenerateIssuesStrategy::class, [
            $this->generatorMock,
            $this->taskFactoryMock
        ]);
        $this->taskMock             = Mockery::mock(DrawIssueTask::class);
        $this->strategy             = new DrawSingleStrategy($this->normalStrategyMock);
    }

    protected function tearDown()
    {
        Mockery::close();

        parent::tearDown();
    }

    public function testDrawIssuesCase1()
    {
        $lotteryId      = 1;
        $date           = Carbon::parse('2017-10-06');
        $issue          = '20171006-001';
        $winningNumbers = ['5', '2', '1', '4', '6'];
        $issues         = [compact('issue')];
        $expects        = [compact('winningNumbers', 'issue')];

        $this->taskFactoryMock
            ->shouldReceive('makeDrawIssueTask')
            ->once()
            ->with($lotteryId)
            ->andReturn($this->taskMock);

        $this->taskMock
            ->shouldReceive('run')
            ->once()
            ->with($issue, $date)
            ->andReturn($winningNumbers);

        $returnValue = $this->strategy->drawIssues($lotteryId, $date, $issues);

        $this->assertSame($expects, $returnValue);
        $this->assertSame($issues, $this->strategy->issues());
    }

    public function testDrawIssuesCase2()
    {
        $lotteryId      = 1;
        $date           = Carbon::parse('2017-10-06');
        $issues         = [
            ['issue' => '20171006-001'],
            ['issue' => '20171006-002'],
        ];
        $result         = [
            ['issue' => '20171006-001', 'winningNumbers' => ['5', '4', '3', '2', '1']],
            ['issue' => '20171006-002', 'winningNumbers' => ['1', '2', '3', '4', '5']],
        ];

        $this->normalStrategyMock
            ->expects($this->any())
            ->method('drawProcess')
            ->will($this->returnValue($result));

        $returnValue = $this->strategy->drawIssues($lotteryId, $date, $issues);

        $this->assertSame($result, $returnValue);
    }
}
