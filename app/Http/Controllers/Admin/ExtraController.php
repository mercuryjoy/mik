<?php

namespace App\Http\Controllers\Admin;

use App\ScanLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ExtraController extends Controller
{
    protected $scan_log;

    public function __construct(ScanLog $scan_log)
    {
        $this->scan_log = $scan_log;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $date_range = $request->input('daterange');
        $dates = explode(' - ', $date_range);
        try {
            $start_date = Carbon::parse($dates[0]);
            $end_date = Carbon::parse($dates[1]);
        } catch(\Exception $e) {
            $end_date = Carbon::today();
            $start_date = $end_date->copy()->subMonth(1)->addDay(1);
            $date_range = implode(' - ', [$start_date->format("Y-m-d"), $end_date->format("Y-m-d")]);
            $request->merge(['daterange' => $date_range]);
        }

        $request->flash();

        $filter_user_name = $request->input('user_name');
        $filter_shop_name = $request->input('shop_name');
        $filter_scan_type = $request->input('scan_type');
        $filter_type = $request->input('type');
        $filter_distribution_name = $request->input('distribution_name');
        $filter_area_id = $request->input('area_id');
        $filter_code = $request->input('code');
        $filter_salesman = $request->input('filter_salesman');

        $logsQuery = $this->scan_log
            ->userName($filter_user_name)
            ->shopName($filter_shop_name)
            ->scanType($filter_scan_type)
            ->distributorName($filter_distribution_name)
            ->code($filter_code)
            ->area($filter_area_id)
            ->type($filter_type)
            ->salesmanName($filter_salesman)
            ->whereBetween('created_at', [$start_date, $end_date->endOfDay()])
            ->where('type', 'scan_send_money_activity')
            ->where('money', '>', 0)
            ->with('code', 'user', 'shop', 'waiter')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');

        if ($request->input('export') === 'xls') {
            $logs = $logsQuery->get();
            Excel::create('扫码额外收入记录', function($excel) use($logs) {
                $excel->sheet('Sheet', function($sheet) use($logs) {
                    $sheet->loadView('admin.extra.index_xls')
                        ->with('scan_logs', $logs);
                });
            })->export('xlsx');
        }

        $logs = $logsQuery->paginate(50);

        return view('admin.extra.index', [
            'scan_logs' => $logs,
            'has_filter' => strlen($filter_scan_type) > 0 || strlen($filter_type) > 0 || strlen($filter_user_name) > 0 || strlen($filter_shop_name) > 0 || strlen($filter_distribution_name) > 0 || strlen($filter_area_id) > 0 || strlen($filter_code) > 0 || strlen($filter_salesman) > 0 || strlen($date_range) > 0,
        ]);
    }
}
