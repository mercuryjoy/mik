<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Contracts\SMSContract;
use App\SMSLog;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;

class SMSController extends Controller
{
    protected $sms_log;

    public function __construct(SMSLog $sms_log)
    {
        $this->sms_log = $sms_log;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $logs = $this->sms_log
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('admin.sms.index', ['sms_logs' => $logs]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, SMSContract $sms)
    {
        $this->validate($request, [
            'telephone' => 'required|regex:' . config('custom.telephone_regex'),
        ], [
            'telephone.*' => '请填入正确手机号'
        ]);

        $sms->sendTestMessage($request->input('telephone'));

        return Redirect::route('admin.sms.index');
    }
}
