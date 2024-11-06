<?php

namespace App\Http\Controllers;

use App\Models\InventoryLog;

class HistoryController extends Controller
{
    public function index()
    {
        $logs = InventoryLog::with('item')->latest()->get();
        return view('history.index', compact('logs'));
    }
}
