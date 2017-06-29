<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lottery;

class LotteryController extends Controller
{
    public function index()
    {
        $data = Lottery::paginate(15);
        return response()->json($data);
    }

    public function create()
    {
        // TODO implements create()
    }

    public function store()
    {
        // TODO implements store()
    }

    public function show()
    {
        // TODO implements show()
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
}
