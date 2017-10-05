<?php

namespace Tests\Unit\Services\IssueDrawing\IssueDrawingStrategy;

use App\Services\IssueDrawerTaskFactory;
use App\Services\IssueDrawing\IssueDrawingStrategy\DrawnResumeStrategy;
use App\Services\IssueDrawing\IssueDrawingStrategy\GenerateIssuesStrategy;
use App\Services\IssueDrawing\IssueDrawingStrategy\IssueDrawingStrategy;
use App\Services\IssueGeneratorService;
use Carbon\Carbon;
use Mockery;
use Tests\TestCase;

class DrawnResumeStrategyTest extends TestCase
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
     * @var \Mockery\MockInterface|\App\Services\IssueDrawing\IssueDrawingStrategy\GenerateIssuesStrategy
     */
    protected $normalStrategyMock;

    /**
     * @var \Mockery\MockInterface|\App\Services\IssueDrawing\IssueDrawingStrategy\IssueDrawingStrategy
     */
    protected $fallbackStrategyMock;

    /**
     * @var \App\Services\IssueDrawing\IssueDrawingStrategy\DrawnResumeStrategy
     */
    protected $strategy;

    protected function setUp()
    {
        parent::setUp();

        $this->generatorMock        = Mockery::mock(IssueGeneratorService::class);
        $this->taskFactoryMock      = Mockery::mock(IssueDrawerTaskFactory::class);

        $this->normalStrategyMock   = Mockery::mock(GenerateIssuesStrategy::class);
        $this->fallbackStrategyMock = Mockery::mock(IssueDrawingStrategy::class);

        $this->normalStrategyMock
            ->shouldReceive('generator')
            ->andReturn($this->generatorMock);

        $this->normalStrategyMock
            ->shouldReceive('taskFactory')
            ->andReturn($this->taskFactoryMock);

        $this->strategy             = new DrawnResumeStrategy($this->normalStrategyMock, $this->fallbackStrategyMock);
    }

    protected function tearDown()
    {
        Mockery::close();

        parent::tearDown();
    }

    /**
     * 当天资料库无任何期号
     */
    public function testDrawCase1()
    {
        $lotteryId   = 1;
        $date        = Carbon::parse('2017-10-05');
        $startNumber = 55;

        $result = [
            ['issue' => '20171005-001', 'winningNumbers' => ['0', '1', '2', '3', '4']],
        ];

        $issues = [
            [
                'issue'             => '20171005-001',
                'belongdate'        => '2017-10-05',
                'salestart'         => Carbon::parse('2017-10-05 06:58:00'),
                'saleend'           => Carbon::parse('2017-10-05 09:03:00'),
                'canneldeadline'    => Carbon::parse('2017-10-05 09:03:00'),
                'earliestwritetime' => Carbon::parse('2017-10-05 09:05:30'),
            ],
        ];

        $this->generatorMock
            ->shouldReceive('notDrawnIssues')
            ->once()
            ->with($lotteryId, $date)
            ->andReturnNull();

        $this->fallbackStrategyMock
            ->shouldReceive('draw')
            ->once()
            ->with($lotteryId, $date, $startNumber)
            ->andReturn($result);

        $this->fallbackStrategyMock
            ->shouldReceive('issues')
            ->once()
            ->withNoArgs()
            ->andReturn($issues);

        $returnArray = $this->strategy->draw($lotteryId, $date, $startNumber);

        $this->assertEquals($result, $returnArray);
        $this->assertEquals($issues, $this->strategy->issues());
    }

    /**
     * 当天资料库有期号需要开号
     */
    public function testDrawCase2()
    {
        $lotteryId   = 1;
        $date        = Carbon::parse('2017-10-05');
        $startNumber = 55;

        $result = [
            ['issue' => '20171005-001', 'winningNumbers' => ['0', '1', '2', '3', '4']],
        ];

        $issues = [
            [
                'issue'             => '20171005-001',
                'belongdate'        => '2017-10-05',
                'salestart'         => Carbon::parse('2017-10-05 06:58:00'),
                'saleend'           => Carbon::parse('2017-10-05 09:03:00'),
                'canneldeadline'    => Carbon::parse('2017-10-05 09:03:00'),
                'earliestwritetime' => Carbon::parse('2017-10-05 09:05:30'),
            ],
        ];

        $notDrawnIssues = [
            [
                'issue'             => '20171005-001',
                'belongdate'        => '2017-10-05',
                'salestart'         => Carbon::parse('2017-10-05 06:58:00'),
                'saleend'           => Carbon::parse('2017-10-05 09:03:00'),
                'canneldeadline'    => Carbon::parse('2017-10-05 09:03:00'),
                'earliestwritetime' => Carbon::parse('2017-10-05 09:05:30'),
            ],
            [
                'issue'             => '20171005-002',
                'belongdate'        => '2017-10-05',
                'salestart'         => Carbon::parse('2017-10-05 09:02:10'),
                'saleend'           => Carbon::parse('2017-10-05 09:07:10'),
                'canneldeadline'    => Carbon::parse('2017-10-05 09:06:25'),
                'earliestwritetime' => Carbon::parse('2017-10-05 09:07:45'),
            ],
        ];

        $this->generatorMock
            ->shouldReceive('notDrawnIssues')
            ->once()
            ->with($lotteryId, $date)
            ->andReturn($notDrawnIssues);

        $this->normalStrategyMock
            ->shouldReceive('drawIssues')
            ->once()
            ->with($lotteryId, $date, $notDrawnIssues)
            ->andReturn($result);

        $this->normalStrategyMock
            ->shouldReceive('issues')
            ->once()
            ->withNoArgs()
            ->andReturn($issues);

        $returnArray = $this->strategy->draw($lotteryId, $date, $startNumber);

        $this->assertEquals($result, $returnArray);
        $this->assertEquals($issues, $this->strategy->issues());
    }

    /**
     * 当天资料库有期号, 但无任何期号需要开号
     */
    public function testDrawCase3()
    {
        $lotteryId   = 1;
        $date        = Carbon::parse('2017-10-05');
        $startNumber = 55;

        $this->generatorMock
            ->shouldReceive('notDrawnIssues')
            ->once()
            ->with($lotteryId, $date)
            ->andReturn([]);

        $returnArray = $this->strategy->draw($lotteryId, $date, $startNumber);

        $this->assertSame([], $returnArray);
        $this->assertSame([], $this->strategy->issues());
    }
}
