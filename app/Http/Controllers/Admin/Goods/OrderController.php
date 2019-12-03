<?php

namespace App\Http\Controllers\Admin\Goods;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Admin\Controller;
use App\StoreOrder;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    protected $storeOrder;

    public function __construct(StoreOrder $storeOrder)
    {
        $this->storeOrder = $storeOrder;
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
            $start_date = Carbon::minValue();
            $end_date = Carbon::maxValue();
        }

        $request->flash();

        $filter_user_name = trim($request->input('filter_user_name'));
        $filter_item_name = trim($request->input('filter_item_name'));
        $filter_shop_name = trim($request->input('filter_shop_name'));
        $filter_salesman_name = trim($request->input('filter_salesman_name'));
        $filter_status = $request->input('filter_status');
        $filter_checked = $request->input('filter_is_checked');
        $filter_item_id = $request->input('item_id');

        $ordersQuery = $this->storeOrder
            ->with('user', 'user.shop', 'salesman')
            ->userName($filter_user_name)
            ->shopName($filter_shop_name)
            ->itemName($filter_item_name)
            ->salesmanName($filter_salesman_name)
            ->status($filter_status)
            ->checked($filter_checked)
            ->itemId($filter_item_id)
            ->whereBetween('created_at', [$start_date, $end_date->endOfDay()])
            ->where('type', 'purchase')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');

        if ($request->input('export') === 'xls') {
            $filterOrders = $ordersQuery->get();
            Excel::create('采购订单', function($excel) use($filterOrders) {
                $excel->sheet('Sheet', function($sheet) use($filterOrders) {
                    $sheet->loadView('admin.goods.order.index_xls')
                        ->with('orders', $filterOrders);
                });
            })->export('xlsx');
        }

        $orders = $ordersQuery->paginate(50);

        return view('admin.goods.order.index', [
            'orders' => $orders,
            'has_filter' => strlen($filter_shop_name) > 0 || strlen($filter_salesman_name) > 0 || strlen($filter_user_name) > 0 || strlen($date_range) > 0 || strlen($filter_item_name) > 0 || strlen($filter_status) > 0 || strlen($filter_checked) > 0,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $order = $this->storeOrder->with('item', 'shop')->find($id);
        if ($order == null) {
            return Redirect::back()
                ->with('message', '订单不存在!')
                ->with('message-type', 'error');
        }

        $new_status = $request->input('status');
        if (!in_array($new_status, ['created', 'shipped'])) {
            return Redirect::back()
                ->with('message', '目标状态不存在!')
                ->with('message-type', 'error');
        }

        $isUpdated = $order->update([
            'status' => $new_status,
            'remarks' => $request->input('remarks')
        ]);
        if ($isUpdated) {
            if ($new_status == 'shipped') {
                // net消息通知
                $date_time = explode(' ', $order->created_at);
                $param = [
                    'ShopName' => $order->shop->name,
                    'CreatedS' => $date_time[0],
                    'CreatedE' => $date_time[1],
                    'Name' => $order->item->name,
                    'Amount' => $order->amount,
                    'Money' => $order->money / 100,
                    'CounselorId' => $order->salesman_id ? $order->salesman_id : '',
                    'Title' => '订单发货通知',
                    'Type' => 2,
                ];
                order_notification($param);
            }

            return Redirect::back()
                ->with('message', '状态修改成功!')
                ->with('message-type', 'success');
        }
        return Redirect::back()
            ->with('message', '状态修改不存在!')
            ->with('message-type', 'error');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function checked(Request $request, $id)
    {
        $order = $this->storeOrder->find($id);
        if ($order == null) {
            return Redirect::back()
                ->with('message', '订单不存在！')
                ->with('message-type', 'error');
        }

        $new_checked = $request->input('is_checked');
        if (!in_array($new_checked, [true, false])) {
            return Redirect::back()
                ->with('message', '目标状态不存在!')
                ->with('message-type', 'error');
        }

        $updateParam = [
            'is_checked' => $new_checked,
            'pay_way' => 'line',
            'is_pay' => true,
            'status' => 'created',
        ];

        if ($order->is_pay) {
            unset($updateParam['pay_way']);
        }

        $isUpdated = $order->update($updateParam);
        if ($isUpdated) {
            return Redirect::back()
                ->with('message', '已通过审核!')
                ->with('message-type', 'success');
        }

        return Redirect::back()
            ->with('message', '审核失败!')
            ->with('message-type', 'error');
    }
}
