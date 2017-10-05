<?php

namespace Tests\Unit\Services\IssueDrawing;

use App\Services\IssueDrawing\IssueDrawnCombine;
use Carbon\Carbon;
use Tests\TestCase;

class IssueDrawnCombineTest extends TestCase
{
    public function testCombine()
    {
        $issues = [
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
            [
                'issue'             => '20171005-003',
                'belongdate'        => '2017-10-05',
                'salestart'         => Carbon::parse('2017-10-05 09:03:10'),
                'saleend'           => Carbon::parse('2017-10-05 09:08:10'),
                'canneldeadline'    => Carbon::parse('2017-10-05 09:07:25'),
                'earliestwritetime' => Carbon::parse('2017-10-05 09:08:45'),
            ],
            [
                'issue'             => '20171005-004',
                'belongdate'        => '2017-10-05',
                'salestart'         => Carbon::parse('2017-10-05 09:12:10'),
                'saleend'           => Carbon::parse('2017-10-05 09:17:10'),
                'canneldeadline'    => Carbon::parse('2017-10-05 09:16:25'),
                'earliestwritetime' => Carbon::parse('2017-10-05 09:17:45'),
            ],
        ];

        $drawn = [
            ['issue' => '20171005-003', 'winningNumbers' => ['7', '4', '5', '7', '8']],
            ['issue' => '20171005-002', 'winningNumbers' => ['6', '6', '1', '4', '3']],
        ];

        $expects = [
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
                'code'              => '66143',
            ],
            [
                'issue'             => '20171005-003',
                'belongdate'        => '2017-10-05',
                'salestart'         => Carbon::parse('2017-10-05 09:03:10'),
                'saleend'           => Carbon::parse('2017-10-05 09:08:10'),
                'canneldeadline'    => Carbon::parse('2017-10-05 09:07:25'),
                'earliestwritetime' => Carbon::parse('2017-10-05 09:08:45'),
                'code'              => '74578',
            ],
            [
                'issue'             => '20171005-004',
                'belongdate'        => '2017-10-05',
                'salestart'         => Carbon::parse('2017-10-05 09:12:10'),
                'saleend'           => Carbon::parse('2017-10-05 09:17:10'),
                'canneldeadline'    => Carbon::parse('2017-10-05 09:16:25'),
                'earliestwritetime' => Carbon::parse('2017-10-05 09:17:45'),
            ],
        ];

        $handler = IssueDrawnCombine::create();
        $handler->setIssues($issues);
        $handler->setDrawn($drawn);

        $returnArray = $handler->combine(function ($issue, $drawn) {
            $issue['code'] = implode('', array_get($drawn, 'winningNumbers', []));
            return $issue;
        });

        $this->assertEquals($expects, $returnArray);
    }
}
