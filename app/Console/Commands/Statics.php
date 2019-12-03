<?php

namespace App\Console\Commands;

use App\Salesman;
use App\SalesmanStatics;
use App\ScanLog;
use App\Shop;
use App\StoreOrder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class Statics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mik:statics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Statics Mik Data';
    protected $store_order;
    protected $scan_log;
    protected $shop;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(StoreOrder $store_order, ScanLog $scan_log, Shop $shop)
    {
        parent::__construct();
        $this->store_order = $store_order;
        $this->scan_log = $scan_log;
        $this->shop = $shop;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $salesmen = $this->getAllSalesmen();

        $data = [];
        if ($salesmen->count() > 0) {

            $start_date = Carbon::yesterday();
            $end_date = Carbon::yesterday()->endOfDay();

            foreach ($salesmen as $salesman) {
                $shop_user_count = $this->getShopUserCount($salesman, $start_date, $end_date);
                $scan_count_money = $this->getScanCountMoney($salesman, $start_date, $end_date);
                $sales_money = $this->getSalesMoney($salesman, $start_date, $end_date);
                $now_time = Carbon::now();

                $user_count = $shop_user_count['user_count'];
                $shop_count = $shop_user_count['shop_count'];
                $total_shop_count = $shop_user_count['total_shop_count'];
                $total_user_count = $shop_user_count['total_user_count'];
                $scan_count = $scan_count_money['scan_count'];
                $scan_money = $scan_count_money['scan_money'];
                $scan_shop_count = $scan_count_money['scan_shop_count'];
                $no_scan_shop_count = $total_shop_count - $scan_shop_count;
                $scan_user_count = $scan_count_money['scan_user_count'];
                $scan_count_percent = $scan_count_money['scan_count_percent'];

                $shop_scan_percent = 0;
                if ($total_shop_count == 0) {
                    $shop_scan_percent = $scan_shop_count * 100;
                } elseif ($total_shop_count > 0) {
                    $shop_scan_percent = $scan_shop_count / $total_shop_count * 100;
                }

                $data[] = [
                    'statics_date' => str_replace(" 23:59:59", "", $end_date),      // 日期
                    'user_count' => $user_count,                 // 今日增长服务员数
                    'shop_count' => $shop_count,                 // 今日增长终端数
                    'total_user_count' => $total_user_count,     // 总服务员数
                    'total_shop_count' => $total_shop_count,     // 总终端数
                    'scan_count' => $scan_count,                 // 今日扫码总数
                    'scan_money' => $scan_money,                // 扫码金额
                    'sales_money' => $sales_money,              // 销售总金额
                    'scan_shop_count' => $scan_shop_count,      // 扫码终端数量
                    'no_scan_shop_count' => $no_scan_shop_count,// 滞销终端数量
                    'scan_user_count' => $scan_user_count,      // 扫码服务员数
                    'scan_count_percent' => $scan_count_percent, // 扫码环比
                    'shop_scan_percent' => $shop_scan_percent,   // 终端活跃率(使用时除以100)
                    'salesman_id' => $salesman,                  // 销售员ID
                    'created_at' => $now_time,
                    'updated_at' => $now_time,
                ];
            }

            SalesmanStatics::insert($data);
            \Log::info('success test');
        }

        $this->info('mik statics successfully.');
    }

    protected function getAllSalesmen()
    {
        return Salesman::pluck('id');
    }

    protected function getShopUserCount($salesman_id, $start_date, $end_date)
    {
        $data = [];
        for($i=0; $i<=1; $i++) {
            $shopObj = Shop::with('users')->where('salesman_id', $salesman_id);

            if ($i == 0) {
                $shops = $shopObj->whereBetween('created_at', [$start_date, $end_date])->get();
            } elseif ($i == 1) {
                $shops = $shopObj->get();
            }

            $shop_count = $shops->count();
            $user_count = 0;
            if ($shop_count > 0) {
                foreach ($shops as $shop) {
                    if ($shop->users) {
                        $user_count += $shop->users->sum(function ($user) {
                            return count($user);
                        });
                    }
                }
            }

            if ($i == 0) {
                $data['shop_count'] = $shop_count;
                $data['user_count'] = $user_count;
            } elseif ($i == 1) {
                $data['total_shop_count'] = $shop_count;
                $data['total_user_count'] = $user_count;
            }
        }

        return $data;
    }

    protected function getScanCountMoney($salesman_id, $start_date, $end_date)
    {
        $scans = $this->scan_log->where('salesman_id', $salesman_id)
            ->whereBetween('created_at', [$start_date, $end_date])
            ->where('type', 'scan_prize')
            ->get();

        $scan_count = $scans->count();
        $scan_money = $scans->sum('money');
        $scan_shop_count = $scans->pluck('shop_id')->unique()->count();     // 扫码终端数量
        $scan_user_count = $scans->pluck('user_id')->unique()->count();     // 扫码服务员数

		$yesterday_static_date = Carbon::today(-30)->toDateString();

		$yesterday_scans = SalesmanStatics::where('statics_date', $yesterday_static_date)->first();

        $yesterday_scan_count = 0;
		if ($yesterday_scans) {
            $yesterday_scan_count = $yesterday_scans->scan_count;
		}

        $scan_count_percent = 0;
        if ($yesterday_scan_count == 0) {
            $scan_count_percent = $scan_count * 100;
        } elseif ($yesterday_scan_count > 0) {
            $scan_count_percent = round(($scan_count - $yesterday_scan_count) / $yesterday_scan_count, 2) * 100;
        }

        return [
            'scan_count' => $scan_count,
            'scan_money' => $scan_money,
            'scan_shop_count' => $scan_shop_count,
            'scan_user_count' => $scan_user_count,
            'scan_count_percent' => $scan_count_percent,
        ];
    }

    protected function getSalesMoney($salesman_id, $start_date, $end_date)
    {
        $sales_money = $this->store_order->where('salesman_id', $salesman_id)
            ->whereBetween('created_at', [$start_date, $end_date])
            ->sum('money');

        return $sales_money;
    }
}
