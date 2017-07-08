<?php

namespace App\Http\Controllers\Issue;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\IssueGeneratorService;

class IssueGenerateController extends Controller
{
    /**
     * @var \App\Services\IssueGeneratorService
     */
    protected $issueGenerator;

    /**
     * IssueGenerateController constructor.
     *
     * @param IssueGeneratorService $issueGenerator
     */
    public function __construct(IssueGeneratorService $issueGenerator)
    {
        $this->issueGenerator = $issueGenerator;
    }

    public function process(Request $request)
    {
        $lotteryid = (string) $request->input('lotteryid');

        $numbers = $this->issueGenerator->generate($lotteryid);

        return response()->json([
            'count' => count($numbers),
        ]);
    }
}
