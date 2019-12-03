<?php

namespace App\Http\Controllers\Net;

use App\Salesman;
use App\SalesmanStatics;
use App\ScanLog;
use App\Shop;
use App\StoreOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StatisticsController extends NetController
{
    /**
     * 营销员总业绩
     * @param Request $request
     * @return array
     */
    public function sales(Request $request)
    {
        $this->validateRules($request, 'sales');

        $salesman_ids = explode(',', $request->input('salesman_id'));
        $data = [];
        foreach ($salesman_ids as $salesman_id) {
            // shop_count
            $shops = Shop::with('users')
                ->where('salesman_id', $salesman_id)
                ->get();

            $shop_count = $shops->count();
            // shop_count
            $user_count = 0;
            if ($shop_count > 0) {
                foreach ($shops as $shop) {
                    if ($shop->users) {
                        $user_count += $shop->users->sum(function ($user) {return count($user);});
                    }
                }
            }
            // 总扫码数和扫码金额
            $scans = ScanLog::where('salesman_id', $salesman_id)
                ->where('type', 'scan_prize')
                ->get();

            $scan_count = $scans->count();
            $scan_money = $scans->sum('money');

            // 总销售额
            $sales_money = StoreOrder::where('salesman_id', $salesman_id)->sum('money');
            $data[] = [
                'user_count' => $user_count,
                'shop_count' => $shop_count,
                'scan_count' => $scan_count,
                'scan_money' => $scan_money,
                'sales_money' => $sales_money,
                'salesman_id'=> $salesman_id,
            ];
        }

        return $this->jsonReturn(200, '查询成功！', $data);
    }
    /**
     * 根据营销员ID查询总业绩
     * @param Request $request
     * @return array
     */
    public function salesCount(Request $request)
    {
        $this->validateRules($request, 'section');

        $salesman_ids = explode(',', $request->input('salesman_id'));
        $start_date = $request->input('start_date') . ' 00:00:00';
        $end_date = $request->input('end_date') . ' 23:59:59';
        $data = [];
        foreach ($salesman_ids as $salesman_id) {
            $scans = ScanLog::where('salesman_id', $salesman_id)
                ->where('type', 'scan_prize')
                ->whereBetween('created_at', [$start_date, $end_date]);

            $scan_count = $scans->count();
            $scan_money = $scans->sum('money');

            // 总销售额
            $sales_money = StoreOrder::where('salesman_id', $salesman_id)->sum('money');

            $data[] = [
                'scan_count' => $scan_count,
                'scan_money' => $scan_money,
                'sales_money' => $sales_money,
                'salesman_id'=> $salesman_id,
            ];
        }

        return $this->jsonReturn(200, '查询成功！', $data);
    }

    /**
     * 根据营销员ID查询总业绩
     * @param Request $request
     * @return array
     */
    public function salesDateCount(Request $request)
    {
        $this->validateRules($request, 'salesDateCount');

        $salesman_ids = explode(',', $request->input('salesman_id'));
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $type = $request->input('type');

        $date_range = [$start_date, $end_date];

        $fields = ['total_shop_count', 'scan_shop_count', 'no_scan_shop_count', 'total_user_count', 'scan_count', 'scan_user_count', 'statics_date', 'salesman_id'];
        $query = SalesmanStatics::select($fields)
            ->whereBetween('statics_date', $date_range)
            ->whereIn('salesman_id', $salesman_ids);
        $data = [];
        $data['statics'] = [
            'total_shop_count' => $query->sum('total_shop_count'),
            'scan_shop_count' => $query->sum('scan_shop_count'),
            'no_scan_shop_count' => $query->sum('no_scan_shop_count'),
            'total_user_count' => $query->sum('total_user_count'),
            'scan_count' => $query->sum('scan_count'),
            'scan_user_count' => $query->sum('scan_user_count'),
        ];

        if ($type == 'query') {
            $queryData = $query->paginate($request->input('pageSize', 20));
            $queryDataArray = $queryData->toArray();
            $data['total'] = $queryDataArray['total'];
            $data['data'] = $queryDataArray['data'];
        } elseif ($type == 'export') {
            $data['total'] = $query->count();
            $data['data'] = [];
            $query_data = $query->get();
            if ($query_data) {
                $data['data'] = $query_data->toArray();
            }
        }
	
        $data_count = count($data['data']);

        return $this->jsonReturn(200, '查询成功！', $data);
    }

    /**
     * 根据营销员ID查询总业绩
     * @param Request $request
     * @return array
     */
    //public function oldSalesDatePercentCount(Request $request)
    //{
    //    $this->validateRules($request, 'salesDatePercentCount');
	//
    //    $salesman_ids = explode(',', $request->input('salesman_id'));
    //    $start_date = $request->input('start_date');
    //    $end_date = $request->input('end_date');
    //    $type = $request->input('type');
	//
    //    $date_range = [$start_date, $end_date];
    //    $fields = [
    //        'statics_date', 'scan_count', 'scan_count_percent', 'total_shop_count', 'shop_count', 'scan_shop_count',
    //        'no_scan_shop_count', 'shop_scan_percent', 'total_user_count', 'scan_user_count', 'salesman_id'
    //    ];
    //    $query = SalesmanStatics::select($fields)
    //        ->whereBetween('statics_date', $date_range);
		//
		//if (strlen($request->input('salesman_id'))) {
		//	$query->whereIn('salesman_id', $salesman_ids);
		//}
	//
    //    $data = [];
    //    if ($type == 'query') {
    //        $queryData = $query->paginate($request->input('pageSize', 20));
    //        $data['total'] = $queryData->total();
    //        $data['data'] = $queryData->items();
    //    } elseif ($type == 'export') {
    //        $data = $query->get();
    //    }
	//
    //    return $this->jsonReturn(200, '查询成功！', $data);
    //}

    /**
     * 根据营销员ID查询总业绩
     * @param Request $request
     * @return array
     */
    public function salesDatePercentCount(Request $request)
    {

        $this->validateRules($request, 'salesDatePercentCount');

        $salesman_ids = explode(',', $request->input('salesman_id'));
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $type = $request->input('type', 'query');
        $page = $request->input('page', 1);
        $pageSize = $request->input('pageSize', 20);

        $every_day_dates = get_date_from_range($start_date, $end_date);

        $datas = [];
        foreach ($every_day_dates as $key=>$every_day_date) {
            $query = SalesmanStatics::select(['statics_date', DB::raw('SUM(scan_count) AS scan_count'), DB::raw('SUM(total_shop_count) AS total_shop_count'), DB::raw('SUM(shop_count) AS shop_count'), DB::raw('SUM(scan_shop_count) AS scan_shop_count'),
                DB::raw('SUM(no_scan_shop_count) AS no_scan_shop_count'), DB::raw('SUM(total_user_count) AS total_user_count'), DB::raw('SUM(user_count) AS user_count'), DB::raw('SUM(scan_user_count) AS scan_user_count')])
                ->where('statics_date', $every_day_date);

            if (strlen($request->input('salesman_id'))) {
                $query->whereIn('salesman_id', $salesman_ids);
            }

            $data = $query->first();

            // count yesterday scan_count
            $yesterday_date = date("Y-m-d",(strtotime($every_day_date) - 3600*24));

            $yesterday_scans = SalesmanStatics::where('statics_date', $yesterday_date);


            if (strlen($request->input('salesman_id'))) {
                $yesterday_scans->whereIn('salesman_id', $salesman_ids);
            }

            $yesterday_scan_count = $yesterday_scans->sum('scan_count');

            if ($yesterday_scan_count == 0) {
                $data->scan_count_percent = $data->scan_count * 100;
            } elseif ($yesterday_scan_count > 0) {
                $data->scan_count_percent = round(($data->scan_count - $yesterday_scan_count) / $yesterday_scan_count, 2) * 100;
            }

            if ($data->statics_date) {
                $datas[] = $data->toArray();
            }
        }

        // print_r($datas);die;

        $static_count = [
            "statics_date" => '合计',
            "scan_count" => array_sum(array_column($datas, 'scan_count')),
            "scan_count_percent" => array_sum(array_column($datas, 'scan_count_percent')),
            "total_shop_count" => array_sum(array_column($datas, 'total_shop_count')),
            "shop_count" => array_sum(array_column($datas, 'shop_count')),
            "scan_shop_count" => array_sum(array_column($datas, 'scan_shop_count')),
            "no_scan_shop_count" => array_sum(array_column($datas, 'no_scan_shop_count')),
            "total_user_count" => array_sum(array_column($datas, 'total_user_count')),
            "user_count" => array_sum(array_column($datas, 'user_count')),
            "scan_user_count" => array_sum(array_column($datas, 'scan_user_count'))
        ];

        $data = [];
        if ($type == 'query') {
            $data['total'] = count($datas);
            $data['data'] = page_array($pageSize, $page, $datas);
            $data['data'][] =  $static_count;
        } elseif ($type == 'export') {
            $data['total'] = count($datas);
            $data['data']=array_values($datas);
            $data['data'][] =  $static_count;

           return $this->jsonReturn(200, '查询成功！', $data);

        }

        return $this->jsonReturn(200, '查询成功！', $data);
    }

    /**
     * 营销员总业绩
     * @param Request $request
     * @return array
     */
    public function salesSection(Request $request)
    {
        $this->validateRules($request, 'section');

        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $salesman_id = $request->input('salesman_id');

        $field = ['statics_date as date', 'user_count', 'total_user_count', 'shop_count', 'total_shop_count', 'scan_count', 'scan_money', 'sales_money', 'salesman_id'];
        $data = SalesmanStatics::select($field)
            ->where('salesman_id', $salesman_id)
            ->whereBetween('statics_date', [$start_date, $end_date])
            ->get();

        return $this->jsonReturn(200, '查询成功！', $data);
    }

    public function scans(Request $request)
    {
        $this->validateRules($request, 'scans');

        $type = $request->input('type');
        $start_date = $request->input('start_date', '1990-01-01 00:00:00');
        $end_date = $request->input('end_date', '2999-01-01 00:00:00');
        $salesman_ids = explode(',', $request->input('salesman_id'));
        $shop_name = $request->input('shop_name');

        $data = [];
        $shop_count = 0;
        $shopsObj = Shop::with(['salesman', 'users.scanLog' => function ($query) use ($start_date, $end_date) {
            $query->whereBetween('created_at', [$start_date, $end_date])
                ->where('type', 'scan_prize');
        }])
            ->select('id', 'name', 'salesman_id')
            ->salesman($salesman_ids)
            ->keyword($shop_name)
            ->orderBy('id', 'desc');


        if ($type == 'query') {
            $shop_data = $shopsObj->paginate($request->input('pageSize', 20));
            $shops = $shop_data->toArray();
            $shop_count = count($shops['data']);

            $data['total'] = $shops['total'];
        } elseif ($type == 'export') {
            $shop_data = $shopsObj->get();
            $shop_count = $shopsObj->count();
            $data['total'] = $shop_count;
        }

        if ($shop_count > 0) {
            foreach ($shop_data as $shop) {
                $scan_count = 0;
                if ($shop->users) {
                    $scan_count = $shop->users->sum(function ($user) {return count($user->scanLog);});
                }

                $data['data'][] = [
                    'shop_id' => $shop->id,
                    'shop_name' => $shop->name,
                    'salesman_name' => isset($shop->salesman->name) ? $shop->salesman->name : '',
                    'scan_count' => $scan_count,
                    'salesman_id' => $shop->salesman_id,
                ];
            }

            $data['statics'] = ['scan_count' => array_sum(array_column($data['data'], 'scan_count'))];

            foreach ($data['data'] as $key=>$value){
                $shop_id[$key] = $value['shop_id'];
                $scan_count_array[$key] = $value['scan_count'];
            }

            array_multisort($scan_count_array,SORT_NUMERIC,SORT_DESC,$shop_id,SORT_STRING,SORT_ASC,$data['data']);
        }

        return $this->jsonReturn(200, '查询成功！', $data);
    }

    public function getScanCountBySalesmanId(Request $request)
    {
        $this->validateRules($request, 'getScanCountBySalesmanId');

        $start_date = $request->input('start_date', '1990-01-01 00:00:00');
        $end_date = $request->input('end_date', '2999-01-01 00:00:00');
        $salesman_id = $request->input('salesman_id');

        $scan_count = ScanLog::select([DB::raw('count(*) as scan_count')])
            ->where('salesman_id', $salesman_id)
            ->whereBetween('created_at', [$start_date, $end_date])
            ->where('type', 'scan_prize')
            ->first();

        $salesman = Salesman::find($salesman_id);

        $scan_count->salesman_id = $salesman->id;
        $scan_count->salesman_name = $salesman->name;
        unset($scan_count->salesman);

        return $this->jsonReturn(200, '查询成功！', $scan_count);
    }

    public function getScanCountByFilter(Request $request)
    {
        $this->validateRules($request, 'getScanCountByFilter');

        $start_date = $request->input('start_date', '1990-01-01 00:00:00');
        $end_date = $request->input('end_date', '2999-01-01 00:00:00');
        $salesman_id = $request->input('salesman_id');
        $shop_name = $request->input('shop_name');

        $scan_count = ScanLog::with(['salesman' => function ($query) {
            $query->withTrashed();
        }, 'shop' => function ($query) {
            $query->withTrashed();
        }])
            ->salesmanId($salesman_id)
            ->shopName($shop_name)
            ->where('type', 'scan_prize')
            ->where('salesman_id', '!=', 0)
            ->whereBetween('created_at', [$start_date, $end_date]);

        $data['statics'] = ['scan_count' => $scan_count->count()];

        $scan_count = $scan_count->select(['salesman_id', 'shop_id', DB::raw('count(*) as scan_count')])
            ->groupBy(['salesman_id', 'shop_id'])
            ->paginate($request->input('pageSize', 20));

        $data['data'] = $scan_count->each(function ($item, $key) {
            $item['salesman_name'] = $item->salesman->name;
            $item['shop_name'] = $item->shop->name;
            unset($item->salesman);
            unset($item->shop);
        });

        return $this->jsonReturn(200, '查询成功！', $data);
    }

    private function validateRules($request, $function_name)
    {
        Validator::extendImplicit('exists_id', function($attribute, $value, $parameters, $validator) {
            $salesman_ids = explode(',', $value);
            foreach ($salesman_ids as $salesman_id) {
                $salesman = Salesman::find($salesman_id);
                if (!$salesman) {
                    return false;
                }
            }

            return true;
        });

        if ($function_name == 'salesDateCount') {
            $this->validate($request, [
                'type'          => 'required|in:query,export',
                'salesman_id'   => 'required|exists_id',
                'start_date'    => 'required|date_format:"Y-m-d"|before:tomorrow',
                'end_date'      => 'required|date_format:"Y-m-d"|before:tomorrow',
            ], [
                'type.required'         => '查询类型必传',
                'type.in'               => '查询类型有误',
                'salesman_id.exists_id' => '数据中有营销员ID不存在',
                'start_date.required'   => '开始时间必传',
                'start_date.date_format'=> '开始时间必须为日期类型',
                'start_date.before'     => '开始时间必须在明天日期之前',
                'end_date.required'     => '结束时间必传',
                'end_date.date_format'  => '结束时间必须为日期类型',
                'end_date.before'       => '开始时间必须在明天日期之前',
            ]);
        } elseif ($function_name == 'salesDatePercentCount') {
            $this->validate($request, [
                'type'          => 'required|in:query,export',
                'start_date'    => 'required|date_format:"Y-m-d"|before:tomorrow',
                'end_date'      => 'required|date_format:"Y-m-d"|before:tomorrow',
            ], [
                'type.in'               => '查询类型有误',
                'start_date.required'   => '开始时间必传',
                'start_date.date_format'=> '开始时间必须为日期类型',
                'start_date.before'     => '开始时间必须在明天日期之前',
                'end_date.required'     => '结束时间必传',
                'end_date.date_format'  => '结束时间必须为日期类型',
                'end_date.before'       => '开始时间必须在明天日期之前',
            ]);
        } elseif ($function_name == 'scans') {
            $this->validate($request, [
                'type'          => 'required|in:query,export',
                // 'salesman_id'   => 'exists_id',
                'start_date'    => 'date_format:"Y-m-d H:i:s"',
                'end_date'      => 'date_format:"Y-m-d H:i:s"|after:start_date',
            ], [
                'salesman_id.exists_id' => '数据中有营销员ID不存在',
                'type.in'               => '查询类型有误',
                'start_date.required'   => '开始时间必传',
                'start_date.date_format'=> '开始时间必须为日期类型',
                'end_date.required'     => '结束时间必传',
                'end_date.date_format'  => '结束时间必须为日期类型',
                'end_date.after'        => '结束时间必须在开始日期之后',
            ]);
        } elseif ($function_name == 'sales') {
            $this->validate($request, [
                'salesman_id' => 'required|exists_id',
            ], [
                'salesman_id.exists_id' => '数据中有营销员ID不存在',
            ]);
        } elseif ($function_name == 'section') {
            $this->validate($request, [
                'salesman_id'   => 'required|exists_id',
                'start_date'    => 'required|date_format:"Y-m-d"|before:tomorrow',
                'end_date'      => 'required|date_format:"Y-m-d"|before:tomorrow',
            ], [
                'salesman_id.exists_id' => '数据中有营销员ID不存在',
                'start_date.required'   => '开始时间必传',
                'start_date.date_format'=> '开始时间必须为日期类型',
                'start_date.before'     => '开始时间必须在明天日期之前',
                'end_date.required'     => '结束时间必传',
                'end_date.date_format'  => '结束时间必须为日期类型',
                'end_date.before'       => '开始时间必须在明天日期之前',
            ]);
        } elseif ($function_name == 'getScanCountBySalesmanId') {
            $this->validate($request, [
                'salesman_id'   => 'required|exists:salesmen,id',
                'start_date'    => 'required|date_format:"Y-m-d H:i:s"',
                'end_date'      => 'required|date_format:"Y-m-d H:i:s"|after:start_date',
            ], [
                'salesman_id.required'  => '营销员ID不能为空',
                'salesman_id.exists'    => '营销员ID不存在',
                'start_date.required'   => '开始时间必传',
                'start_date.date_format'=> '开始时间必须为日期类型',
                'end_date.required'     => '结束时间必传',
                'end_date.date_format'  => '结束时间必须为日期类型',
                'end_date.after'        => '结束时间必须在开始日期之后',
            ]);
        } elseif ($function_name == 'getScanCountByFilter') {
            $this->validate($request, [
                'salesman_id'   => 'exists:salesmen,id',
                'start_date'    => 'date_format:"Y-m-d H:i:s"',
                'end_date'      => 'date_format:"Y-m-d H:i:s"|after:start_date',
            ], [
                'salesman_id.exists'    => '营销员ID不存在',
                'start_date.required'   => '开始时间必传',
                'start_date.date_format'=> '开始时间必须为日期类型',
                'end_date.required'     => '结束时间必传',
                'end_date.date_format'  => '结束时间必须为日期类型',
                'end_date.after'        => '结束时间必须在开始日期之后',
            ]);
        }
    }
}
