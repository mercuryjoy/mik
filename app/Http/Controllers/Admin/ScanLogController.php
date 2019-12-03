<?php

namespace App\Http\Controllers\Admin;

use App\ScanLog;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ScanLogController extends Controller
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
        $filter_distribution_name = $request->input('distribution_name');
        $filter_area_id = $request->input('area_id');
        $filter_user_id = $request->input('filter_user_id');
        $filter_code = $request->input('code');
        $filter_salesman = $request->input('filter_salesman');

        $logsQuery = $this->scan_log->userName($filter_user_name)
            ->shopName($filter_shop_name)
            ->distributorName($filter_distribution_name)
            ->code($filter_code)
            ->area($filter_area_id)
            ->filterSalesmanName($filter_salesman)
            ->userId($filter_user_id)
            ->whereBetween('created_at', [$start_date, $end_date->endOfDay()])
            ->where('type', 'scan_prize')
            ->with(['distributor' => function ($query) {
                $query->withTrashed();
            }, 'code', 'user' => function ($query) {
                $query->withTrashed();
            }, 'shop' => function ($query) {
                $query->withTrashed();
            }, 'shop.category', 'salesman'])
            ->orderBy('id', 'desc');

        if ($request->input('export') === 'xls') {
            $logs = $logsQuery->get();
            Excel::create('扫码记录', function($excel) use($logs) {
                $excel->sheet('Sheet', function($sheet) use($logs) {
                    $sheet->loadView('admin.scan.index_xls')
                        ->with('scan_logs', $logs);
                });
            })->export('xlsx');
        }

        $logs = $logsQuery->paginate(50);
        return view('admin.scan.index', [
            'scan_logs' => $logs,
            'has_filter' => strlen($filter_user_name) > 0 ||strlen($filter_user_id) > 0 || strlen($filter_shop_name) > 0 || strlen($filter_distribution_name) > 0 || strlen($filter_area_id) > 0 || strlen($filter_code) > 0 || strlen($filter_salesman) > 0,
        ]);
    }

}
