<?php

namespace App\Http\Controllers\Admin;

use App\ScanLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ScanCouponLogsController extends Controller
{
    protected $couponLog;

    public function __construct(ScanLog $couponLog)
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

        $user_name = $request->input('user_name');
        $scan_user_name = $request->input('scan_user_name');
        $shop_name = $request->input('shop_name');

        $couponQuery = $this->couponLog->with(['user', 'shop'])
            ->userName($user_name)
            ->shopName($shop_name)
            ->scanUserName($scan_user_name)
            ->where('type', 'scan_coupon')
            ->whereBetween('created_at', [$start_date, $end_date->endOfDay()])
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');

        if ($request->input('export') === 'xls') {
            $couponLogs = $couponQuery->get();
            Excel::create('优惠券核销记录 ', function ($excel) use ($couponLogs) {
                $excel->sheet('Sheet', function ($sheet) use ($couponLogs) {
                    $sheet->loadView('admin.coupons.index_xls')
                        ->with('couponLogs', $couponLogs);
                });
            })->export('xlsx');
        }

        $couponLogs = $couponQuery->paginate(50);

        return view('admin.coupons.index',
            [
                'couponLogs' => $couponLogs,
                'has_filter' => strlen($user_name) > 0 || strlen($scan_user_name) > 0 || strlen($shop_name) > 0 || strlen($date_range) > 0,
            ]
        );

    }
}
