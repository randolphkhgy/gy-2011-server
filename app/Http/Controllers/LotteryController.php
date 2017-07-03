<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LotteryService;

class LotteryController extends Controller
{
    /**
     * @var \App\Services\LotteryService
     */
    protected $lotterySer;

    /**
     * LotteryController constructor.
     *
     * @param \App\Services\LotteryService $lotterySer
     */
    public function __construct(LotteryService $lotterySer)
    {
        $this->lotterySer = $lotterySer;
    }

    public function index()
    {
        $data = $this->lotterySer->allLotteries(true);
        return response()->json([
            'data' => $data
        ]);
    }

    public function create()
    {
        // TODO implements create()
    }

    public function store()
    {
        // TODO implements store()
    }

    public function show($id)
    {
        $data = $this->lotterySer->get($id, true);
        abort_unless($data, 404);

        return response()->json([
            'data' => $data,
        ]);
    }

    public function edit()
    {
        // TODO implements edit
    }

    public function update()
    {
        // TODO implements update()
    }

    public function destroy()
    {
        // TODO implements destroy()
    }

    public function shuzi()
    {
        $data = $this->lotterySer->allShuzi(true);
        return response()->json([
            'data' => $data,
        ]);
    }

    public function shuzivn()
    {
        $data = $this->lotterySer->allShuzivn(true);
        return response()->json([
            'data' => $data,
        ]);
    }
}
