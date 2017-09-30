<?php

namespace Tests\Unit\Services;

use App\Services\IssueDrawerService;
use App\Services\IssueDrawing\IssueDrawingStrategy\IssueDrawingStrategy;
use App\Services\IssueDrawing\SmartDateDrawerFactory;
use App\Services\IssueGeneratorService;
use Carbon\Carbon;
use Mockery;
use Tests\TestCase;

class IssueDrawerServiceTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface|\App\Services\IssueGeneratorService
     */
    protected $generatorMock;

    /**
     * @var \App\Services\IssueDrawerService
     */
    protected $drawer;

    /**
     * @var \Mockery\MockInterface|\App\Services\IssueDrawing\SmartDateDrawerFactory
     */
    protected $drawerMock;

    /**
     * @var \Mockery\MockInterface|\App\Services\IssueDrawing\IssueDrawingStrategy\IssueDrawingStrategy
     */
    protected $strategyMock;

    protected function setUp()
    {
        parent::setUp();

        $this->generatorMock      = Mockery::mock(IssueGeneratorService::class);
        $this->drawerMock         = Mockery::mock(SmartDateDrawerFactory::class);
        $this->strategyMock       = Mockery::mock(IssueDrawingStrategy::class);
        $this->drawer             = new IssueDrawerService(
            $this->generatorMock,
            $this->drawerMock
        );
    }

    protected function tearDown()
    {
        Mockery::close();

        parent::tearDown();
    }

    public function testDrawDate()
    {
        $lotteryid = 1;
        $date = new Carbon('2017-07-20');

        $issues = [[
            'issue' => '20170720-001',
            'earliestwritetime' => new Carbon('2017-07-17 00:05:30'),
            'code' => '',
        ], [
            'issue' => '20170720-002',
            'earliestwritetime' => new Carbon('2017-07-17 00:10:30'),
            'code' => '',
        ]];

        $taskReturns = [[
            'winningNumbers' => ['3', '6', '5', '8', '2'],
            'issue' => '20170720-001',
        ], [
            'winningNumbers' => ['0', '9', '1', '2', '3'],
            'issue' => '20170720-002',
        ]];

        $expects = [[
            'issue' => '20170720-001',
            'earliestwritetime' => new Carbon('2017-07-17 00:05:30'),
            'code' => '36582',
        ], [
            'issue' => '20170720-002',
            'earliestwritetime' => new Carbon('2017-07-17 00:10:30'),
            'code' => '09123',
        ]];

        $this->drawerMock
            ->shouldReceive('make')
            ->once()
            ->with($lotteryid)
            ->andReturn($this->strategyMock);

        $this->strategyMock
            ->shouldReceive('draw')
            ->with($lotteryid, $date)
            ->andReturn($taskReturns);

        $this->strategyMock
            ->shouldReceive('issues')
            ->withNoArgs()
            ->andReturn($issues);

        $this->generatorMock
            ->shouldReceive('save')
            ->once()
            ->andReturn($expects);

        $returnArray = $this->drawer->drawDate($lotteryid, $date);
        $this->assertSame($expects, $returnArray);
    }
}
