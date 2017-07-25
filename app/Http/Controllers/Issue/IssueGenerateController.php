<?php

namespace App\Http\Controllers\Issue;

use Carbon\Carbon;
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

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function process(Request $request)
    {
        $lotteryid = (string) $request->input('lotteryid');
        $date      = new Carbon($request->input('date'));

        $numbers = $this->issueGenerator->generate($lotteryid, $date);

        return response()->json([
            'count' => count($numbers),
        ]);
    }
}
