<?php

namespace App\Http\Controllers\Admin\Goods;

use App\OrderDrawback;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Admin\Controller;
use App\StoreOrder;

class CanceledController extends Controller
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

        $filter_user_name = $request->input('filter_user_name');
        $filter_item_name = $request->input('filter_item_name');
        $filter_shop_name = trim($request->input('filter_shop_name'));
        $filter_salesman_name = trim($request->input('filter_salesman_name'));
        $filter_status = $request->input('filter_status');
        $filter_checked = $request->input('filter_is_checked');
        $filter_item_id = $request->input('item_id');

        $orders = $this->storeOrder
            ->with('user', 'user.shop', 'salesman')
            ->userName($filter_user_name)
            ->itemName($filter_item_name)
            ->shopName($filter_shop_name)
            ->salesmanName($filter_salesman_name)
            ->status($filter_status)
            ->checked($filter_checked)
            ->itemId($filter_item_id)
            ->whereBetween('created_at', [$start_date, $end_date->endOfDay()])
            ->where('type', 'purchase')
            ->whereIn('status', ['drawback', 'canceled'])
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(50);

        return view('admin.goods.canceled.index', [
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
        $order = $this->storeOrder->find($id);
        if ($order == null) {
            return Redirect::back()
                ->with('message', '订单不存在!')
                ->with('message-type', 'error');
        }

        $new_status = $request->input('status');
        if (!in_array($new_status, ['canceled', 'drawback'])) {
            return Redirect::back()
                ->with('message', '目标状态不存在!')
                ->with('message-type', 'error');
        }

        $isUpdated = $order->update([
            'status' => $new_status,
        ]);
        if ($isUpdated) {
            $drawbackRes = OrderDrawback::create([
                'user_id' => $order->user_id,
                'store_order_id' => $order->id,
                'pay_money' => $order->money,
                'drawback_money' => $order->money,
                'pay_way' => $order->pay_way,
                'status' => 'check',
                'source' => 'cancel',
            ]);

            if ($drawbackRes) {
                return Redirect::back()
                    ->with('message', '状态修改成功!')
                    ->with('message-type', 'success');
            }
        }
        return Redirect::back()
            ->with('message', '状态修改失败!')
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
        ];


        if (!$order->is_pay) {
            $updateParam = [
                'is_checked' => $new_checked,
                'pay_way' => 'line',
                'is_pay' => true,
            ];
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
