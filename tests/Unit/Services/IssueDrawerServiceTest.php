<?php

namespace Tests\Unit\Services;

use App\GyTreasure\CodeFormatter;
use App\Repositories\IssueInfoRepository;
use App\Services\IssueDrawerFactory;
use App\Services\IssueDrawerService;
use App\Services\IssueGeneratorService;
use Carbon\Carbon;
use GyTreasure\Tasks\DrawDateTask;
use Mockery;
use Tests\TestCase;

class IssueDrawerServiceTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface|\App\Repositories\IssueInfoRepository
     */
    protected $issueInfoRep;

    /**
     * @var \Mockery\MockInterface|\App\Services\IssueGeneratorService
     */
    protected $generatorMock;

    /**
     * @var \Mockery\MockInterface|\App\Services\IssueDrawerFactory
     */
    protected $factoryMock;

    /**
     * @var \Mockery\MockInterface|\GyTreasure\Tasks\DrawDateTask
     */
    protected $taskMock;

    /**
     * @var \App\Services\IssueDrawerService
     */
    protected $drawer;

    protected function setUp()
    {
        parent::setUp();

        $this->issueInfoRep     = Mockery::mock(IssueInfoRepository::class);
        $this->generatorMock    = Mockery::mock(IssueGeneratorService::class);
        $this->factoryMock      = Mockery::mock(IssueDrawerFactory::class);
        $this->taskMock         = Mockery::mock(DrawDateTask::class);
        $this->drawer           = new IssueDrawerService(
            $this->issueInfoRep,
            $this->generatorMock,
            $this->factoryMock
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

        $issues = [
            [
                'issue' => '20170720-001',
                'earliestwritetime' => new Carbon('2017-07-17 00:05:30'),
                'code' => '',
            ],
            [
                'issue' => '20170720-002',
                'earliestwritetime' => new Carbon('2017-07-17 00:10:30'),
                'code' => '',
            ],
        ];

        $draws = [[
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

        $this->generatorMock
            ->shouldReceive('generate')
            ->once()
            ->with($lotteryid, $date)
            ->andReturnUsing(function () use ($issues) {
                foreach ($issues as $row) {
                    yield $row;
                }
            });

        $this->factoryMock
            ->shouldReceive('makeDrawDateTask')
            ->once()
            ->with($lotteryid)
            ->andReturn($this->taskMock);

        $this->taskMock
            ->shouldReceive('run')
            ->once()
            ->with($date, array_column($issues, 'issue'))
            ->andReturn($draws);

        $this->generatorMock
            ->shouldReceive('save')
            ->once()
            ->andReturn($expects);

        $returnArray = $this->drawer->drawDate($lotteryid, $date);
        $this->assertEquals($expects, $returnArray);
    }
}
