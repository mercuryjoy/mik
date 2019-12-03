<?php

namespace App\Http\Controllers\Admin\Goods;

use App\OrderDrawback;
use App\StoreOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Admin\Controller;

class DrawbackController extends Controller
{
    protected $orderDrawback;

    public function __construct(OrderDrawback $orderDrawback)
    {
        $this->orderDrawback = $orderDrawback;
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

        $filter_order_id = $request->input('filter_order_id');

        $orderDrawbacks = $this->orderDrawback
            ->orderId($filter_order_id)
            ->whereBetween('created_at', [$start_date, $end_date->endOfDay()])
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(50);

        return view('admin.goods.drawback.index', [
            'orderDrawbacks' => $orderDrawbacks,
            'has_filter' => strlen($filter_order_id) > 0 || strlen($date_range) > 0,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $orderDrawback = $this->orderDrawback->find($id);

        if ($orderDrawback == null) {
            return Redirect::back()
                ->with('message', '退款订单不存在！')
                ->with('message-type', 'error');
        }

        $status = $request->input('status');
        if (!in_array($status, ['check', 'finished'])) {
            return Redirect::back()
                ->with('message', '目标状态不存在!')
                ->with('message-type', 'error');
        }

        $updateParam = [
            'status' => $status,
        ];
        $isUpdated = $orderDrawback->update($updateParam);

        if ($isUpdated) {
            $order = StoreOrder::find($orderDrawback->store_order_id);
            if ($order == null) {
                return Redirect::back()
                    ->with('message', '采购订单不存在！')
                    ->with('message-type', 'error');
            }
            $order->status = 'canceled';
            $orderSave = $order->save();

            if ($orderSave) {
                return Redirect::back()
                    ->with('message', '审核成功!')
                    ->with('message-type', 'success');
            }
        }

        return Redirect::back()
            ->with('message', '审核失败!')
            ->with('message-type', 'error');
    }
}
