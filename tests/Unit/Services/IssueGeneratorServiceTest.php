<?php

namespace Tests\Unit\Services;

use App\Models\IssueInfo;
use App\Repositories\IssueInfoDateRepository;
use App\Repositories\IssueInfoRepository;
use App\Repositories\LotteryRepository;
use App\Services\IssueGeneratorService;
use Carbon\Carbon;
use Mockery;
use Tests\TestCase;

class IssueGeneratorServiceTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface|\App\Repositories\LotteryRepository
     */
    protected $lotteryRepoMock;

    /**
     * @var \Mockery\MockInterface|\App\Models\IssueInfo
     */
    protected $issueInfoMock;

    /**
     * @var \Mockery\MockInterface|\App\Repositories\IssueInfoRepository
     */
    protected $issueInfoRepoMock;

    /**
     * @var \Mockery\MockInterface|\GyTreasure\Tasks\DrawDateTask
     */
    protected $drawDateTaskMock;

    /**
     * @var \Mockery\MockInterface|\App\GyTreasure\DrawDateTaskFactory
     */
    protected $drawDateTaskFactoryMock;

    /**
     * @var \Mockery\MockInterface|\App\Repositories\IssueInfoDateRepository
     */
    protected $issueInfoDateRepoMock;

    /**
     * @var \App\Services\IssueGeneratorService
     */
    protected $service;

    protected function setUp()
    {
        parent::setUp();

        $this->lotteryRepoMock          = Mockery::mock(LotteryRepository::class);
        $this->issueInfoRepoMock        = Mockery::mock(IssueInfoRepository::class);
        $this->issueInfoMock            = Mockery::mock(IssueInfo::class);
        $this->issueInfoDateRepoMock    = Mockery::mock(IssueInfoDateRepository::class);
        $this->service                  = new IssueGeneratorService(
            $this->lotteryRepoMock,
            $this->issueInfoRepoMock,
            $this->issueInfoDateRepoMock
        );
    }

    protected function tearDown()
    {
        Mockery::close();
        
        parent::tearDown();
    }

    public function testGenerate()
    {
        $lotteryid = 1;
        $date = Carbon::today();

        list($issuerule, $issueset, $count) = $this->_issueRules();

        $this->lotteryRepoMock
            ->shouldReceive('find')
            ->once()
            ->with($lotteryid)
            ->andReturn((object) compact('lotteryid', 'issuerule', 'issueset'));

        // 只验证有多少资料，实际产生的资料验证已在 gy-treasure 有单元测试，不需重复撰写
        $returnArray = iterator_to_array($this->service->generate($lotteryid, $date));
        $this->assertEquals($count, count($returnArray));
    }

    protected function _issueRules()
    {
        $issuerule = 'Ymd-[n3]|0,1,0';
        $issueset = [
            [
                'starttime' => '00:00:00',
                'firstendtime' => '00:05:00',
                'endtime' => '01:55:00',
                'cycle' => 300,
                'endsale' => 35,
                'inputcodetime' => 30,
                'droptime' => 35,
                'status' => 1,
                'sort' => 0,
            ], [
                'starttime' => '07:00:00',
                'firstendtime' => '10:00:00',
                'endtime' => '22:00:00',
                'cycle' => 600,
                'endsale' => 45,
                'inputcodetime' => 20,
                'droptime' => 45,
                'status' => 1,
                'sort' => 1,
            ], [
                'starttime' => '21:59:50',
                'firstendtime' => '22:05:00',
                'endtime' => '00:00:00',
                'cycle' => 300,
                'endsale' => 35,
                'inputcodetime' => 30,
                'droptime' => 35,
                'status' => 1,
                'sort' => 2,
            ],
        ];

        $count = 120;

        return [$issuerule, $issueset, $count];
    }
}
