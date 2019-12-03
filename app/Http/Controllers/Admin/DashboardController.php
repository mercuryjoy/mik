<?php

namespace App\Http\Controllers\Admin;

use App\FundingPoolLog;
use App\ScanLog;
use App\User;

class DashboardController extends Controller
{
    public function index() {
        $scan_count = ScanLog::count();
        $scan_money_total = ScanLog::sum('money');
        $user_money_balance_total = User::sum('money_balance');

        $last_funding_pool_log = FundingPoolLog::orderBy('id', 'desc')->first();

        return view('admin.dashboard.index')->with([
            'scan_count' => $scan_count,
            'scan_money_total' => $scan_money_total,
            'pool_balance' => $last_funding_pool_log != null ? $last_funding_pool_log->balance : 0,
            'user_money_balance_total' => $user_money_balance_total,
        ]);
    }
}
