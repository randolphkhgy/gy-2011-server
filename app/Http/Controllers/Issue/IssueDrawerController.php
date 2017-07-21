<?php

namespace App\Http\Controllers\Issue;

use App\Http\Controllers\Controller;
use App\Services\IssueDrawerService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class IssueDrawerController extends Controller
{
    /**
     * @var \App\Services\IssueDrawerService
     */
    protected $drawer;

    /**
     * IssueDrawerController constructor.
     * @param \App\Services\IssueDrawerService $drawer
     */
    public function __construct(IssueDrawerService $drawer)
    {
        $this->drawer = $drawer;
    }

    public function drawDate(Request $request)
    {
        $lotteryId = (string) $request->input('lotteryid');
        $date      = new Carbon($request->input('date'));

        $data      = $this->drawer->drawDate($lotteryId, $date);

        return response()->json([
            'count' => count($data),
        ]);
    }
}