<?php

namespace App\Http\Controllers\Admin;

use App\ScanWarning;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ScanWarningController extends Controller
{
    protected $couponLog;

    public function __construct(ScanWarning $couponLog)
    {
        $this->couponLog = $couponLog;
    }

    public function index(Request $request)
    {
        $date_range = $request->input('daterange');
        $dates = explode(' - ', $date_range);
        try {
            $start_date = Carbon::parse($dates[0]);
            $end_date = Carbon::parse($dates[1]);
        } catch(\Exception $e) {
            $start_date = Carbon::minValue();
            $end_date = Carbon::maxValue();
        }

        $request->flash();

        $filter_user_id = trim($request->input('filter_user_id'));
        $filter_user_name = trim($request->input('filter_user_name'));
        $filter_net_user_id = trim($request->input('filter_net_user_id'));
        $filter_net_user_name = trim($request->input('filter_net_user_name'));
        $filter_shop_id = trim($request->input('filter_shop_id'));
        $filter_shop_name = trim($request->input('filter_shop_name'));
        $filter_warning_type = trim($request->input('filter_warning_type'));

        $warningsQuery = $this->couponLog
            ->with(['user', 'shop'])
            ->userId($filter_user_id)
            ->userName($filter_user_name)
            ->shopId($filter_shop_id)
            ->shopName($filter_shop_name)
            ->netUserId($filter_net_user_id)
            ->netUserName($filter_net_user_name)
            ->warningType($filter_warning_type)
            ->whereBetween('created_at', [$start_date, $end_date])
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');

        // if ($request->input('export') === 'xls') {
        //     $couponLogs = $couponQuery->get();
        //     Excel::create('优惠券核销记录 ', function ($excel) use ($couponLogs) {
        //         $excel->sheet('Sheet', function ($sheet) use ($couponLogs) {
        //             $sheet->loadView('admin.coupons.index_xls')
        //                 ->with('couponLogs', $couponLogs);
        //         });
        //     })->export('xlsx');
        // }

        $warnings = $warningsQuery->paginate(15);

        // print_r($warnings->toArray());die;

        return view('admin.coupons.warning',
            [
                'warnings' => $warnings,
                'has_filter' => strlen($filter_user_name) > 0 || strlen($filter_net_user_name) > 0 || strlen($filter_shop_name) > 0 || strlen($date_range) > 0
                || strlen($filter_user_id) > 0 || strlen($filter_shop_id) > 0 || strlen($filter_warning_type) > 0,
            ]
        );

    }
}
