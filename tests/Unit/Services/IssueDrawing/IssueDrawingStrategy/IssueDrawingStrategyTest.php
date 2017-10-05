<?php

namespace Tests\Unit\Services\IssueDrawing\IssueDrawingStrategy;

use App\Services\IssueDrawerTaskFactory;
use App\Services\IssueDrawing\IssueDrawingStrategy\IssueDrawingStrategy;
use App\Services\IssueGeneratorService;
use Carbon\Carbon;
use Mockery;
use Tests\TestCase;

class IssueDrawingStrategyTest extends TestCase
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
     * @var \Tests\Unit\Services\IssueDrawing\IssueDrawingStrategy\StubIssueDrawingStrategy
     */
    protected $strategy;

    public function setUp()
    {
        parent::setUp();

        $this->generatorMock        = Mockery::mock(IssueGeneratorService::class);
        $this->taskFactoryMock      = Mockery::mock(IssueDrawerTaskFactory::class);

        $this->strategy             = new StubIssueDrawingStrategy($this->generatorMock, $this->taskFactoryMock);
    }

    protected function tearDown()
    {
        Mockery::close();

        parent::tearDown();
    }

    public function testGenerator()
    {
        $this->assertSame($this->generatorMock, $this->strategy->generator());
    }

    public function testTaskFactory()
    {
        $this->assertSame($this->taskFactoryMock, $this->strategy->taskFactory());
    }

    public function testGenerateIssues()
    {
        $lotteryId   = 1;
        $date        = Carbon::parse('2017-10-05');
        $startNumber = 56;

        $issues = [
            ['issue' => '20171005-001'],
            ['issue' => '20171005-002'],
            ['issue' => '20171005-003'],
            ['issue' => '20171005-004'],
        ];

        $this->generatorMock
            ->shouldReceive('generate')
            ->once()
            ->with($lotteryId, $date, $startNumber)
            ->andReturnUsing(function () use ($issues) {
                foreach ($issues as $issue) {
                    yield $issue;
                }
            });

        $returnArray = $this->strategy->callGenerateIssues($lotteryId, $date, $startNumber);

        $this->assertSame($issues, $returnArray);
    }

    public function testFilterNeededDrawing()
    {
        $issues = [
            [
                'issue' => '20171005-001',
                'earliestwritetime' => Carbon::parse('-2 hours'),
            ],
            [
                'issue' => '20171005-002',
                'earliestwritetime' => Carbon::parse('-1 hour'),
            ],
            [
                'issue' => '20171005-004',
                'earliestwritetime' => Carbon::parse('+1 hour'),
            ],
            [
                'issue' => '20171005-003',
                'earliestwritetime' => Carbon::now(),
            ],
        ];

        $returnArray = $this->strategy->callFilterNeededDrawing($issues);

        $expects = $issues;
        unset($expects[2]);

        $this->assertSame($expects, $returnArray);
    }
}

class StubIssueDrawingStrategy extends IssueDrawingStrategy
{
    public function draw($lotteryId, Carbon $date, $startNumber = null)
    {
        throw new \Exception();
    }

    /**
     * @param  int       $lotteryId
     * @param  \Carbon\Carbon  $date
     * @param  int|null  $startNumber
     * @return array
     */
    public function callGenerateIssues($lotteryId, Carbon $date, $startNumber = null)
    {
        return $this->generateIssues($lotteryId, $date, $startNumber);
    }

    /**
     * @param  array  $array
     * @return array
     */
    public function callFilterNeededDrawing(array $array)
    {
        return $this->filterNeededDrawing($array);
    }
}
