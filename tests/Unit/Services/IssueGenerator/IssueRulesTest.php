<?php

namespace Tests\Unit\Services\IssueGenerator;

use Tests\TestCase;

use App\Services\IssueGenerator\IssueRules;

class IssueRulesTest extends TestCase
{
    public function testInitRulesCase1()
    {
        $issuerule = 'Ymd[n3]|0,1,0';
        $format    = 'Ymd[n3]';
        $resetWhen = ['year' => false, 'month' => true, 'day' => false];

        $rule1 = new IssueRules($issuerule);
        $this->assertSame($format, $rule1->format);
        $this->assertSame($resetWhen, $rule1->resetWhen);
    }

    public function testInitRulesCase2()
    {
        $issuerule = 'Ymd[n3]|1,0,0';
        $format    = 'Ymd[n3]';
        $resetWhen = ['year' => true, 'month' => false, 'day' => false];

        $rule1 = new IssueRules($issuerule);
        $this->assertSame($format, $rule1->format);
        $this->assertSame($resetWhen, $rule1->resetWhen);
    }

    public function testInitRulesCase3()
    {
        $issuerule = 'Ymd[n3]|0,0,1';
        $format    = 'Ymd[n3]';
        $resetWhen = ['year' => false, 'month' => false, 'day' => true];

        $rule1 = new IssueRules($issuerule);
        $this->assertSame($format, $rule1->format);
        $this->assertSame($resetWhen, $rule1->resetWhen);
    }
}