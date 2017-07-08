<?php

namespace Tests\Unit\Services;

use App\Models\IssueInfo;
use App\Models\Lottery;
use App\Repositories\LotteryRepository;
use App\Services\IssueGeneratorService;
use Mockery;
use Tests\TestCase;

class IssueGeneratorServiceTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface|\App\Repositories\LotteryRepository
     */
    protected $lotteryRepoMock;

    /**
     * @var \Mockery\MockInterface|\App\Models\Lottery
     */
    protected $lotteryMock;

    /**
     * @var \Mockery\MockInterface|\App\Models\IssueInfo
     */
    protected $issueInfoMock;

    /**
     * @var \App\Services\IssueGeneratorService
     */
    protected $service;

    protected function setUp()
    {
        parent::setUp();

        $this->lotteryRepoMock = Mockery::mock(LotteryRepository::class);
        $this->lotteryMock     = Mockery::mock(Lottery::class);
        $this->issueInfoMock   = Mockery::mock(IssueInfo::class);
        $this->service         = new IssueGeneratorService($this->lotteryRepoMock, $this->issueInfoMock);
    }

    public function testGenerate()
    {
//        $lotteryId = 1;
//
//        $this->
//
//        $this->service->generate($lotteryId);
    }
}