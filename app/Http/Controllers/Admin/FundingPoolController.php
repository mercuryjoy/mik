<?php

namespace App\Http\Controllers\Admin;

use App\FundingPoolLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;


class FundingPoolController extends Controller
{
    private $fundingPoolLog;

    public function __construct(FundingPoolLog $fundingPoolLog)
    {
        $this->fundingPoolLog = $fundingPoolLog;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fundingPoolLogs = $this->fundingPoolLog
                ->with('admin')
                ->orderBy('id', 'desc')->paginate(20);

        return view('admin.fundingpool.index', ['fundingPoolLogs' => $fundingPoolLogs]);
    }

    public function summary()
    {
        $last_funding_pool_log = FundingPoolLog::orderBy('id', 'desc')->first();

        return view('admin.fundingpool.summary', [
            'pool_balance' => $last_funding_pool_log != null ? $last_funding_pool_log->balance : 0,
        ]);
    }

    public function deposit(Request $request) {
        $this->validate($request, [
            'amount' => 'required|numeric|min:1'
        ], [
            'amount.*' => '金额为必填项,请填入>1的数字'
        ]);

        $amount = intval($request->input('amount') * 100);

        $last_funding_pool_log = FundingPoolLog::orderBy('id', 'desc')->first();

        $log = new FundingPoolLog(['amount' => $amount]);
        $log->balance = $last_funding_pool_log->balance + $amount;
        $log->type = 'deposit';
        $log->admin_id = Auth::user()->id;

        $log->save();

        if ($log->id != 0) {
            return Redirect::back()
                ->with('message', '充值成功!')
                ->with('message-type', 'success');
        }
        return Redirect::back()
            ->withInput()
            ->withErrors($log->errors());
    }
}
