<?php

namespace App\Http\Controllers\Admin\Store;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Admin\Controller;
use App\StoreOrder;
use Carbon\Carbon;
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
            $end_date = Carbon::today();
            $start_date = $end_date->copy()->subMonth(1)->addDay(1);
            $date_range = implode(' - ', [$start_date->format("Y-m-d"), $end_date->format("Y-m-d")]);
            $request->merge(['daterange' => $date_range]);
        }

        $request->flash();

        $filter_user_name = $request->input('filter_user_name');
        $filter_item_name = $request->input('filter_item_name');
        $filter_status = $request->input('status');
        $filter_item_id = $request->input('item_id');

        $orderObj = $this->storeOrder
            ->with('user', 'item', 'user.shop', 'UserPointLog')
            ->userName($filter_user_name)
            ->itemName($filter_item_name)
            ->status($filter_status)
            ->itemId($filter_item_id)
            ->whereBetween('created_at', [$start_date, $end_date->endOfDay()])
            ->where('type', 'exchange')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');

        if ($request->input('export') === 'xls') {
            $ordersExports = $orderObj->get();
            Excel::create('订单记录', function($excel) use($ordersExports) {
                $excel->sheet('Sheet', function($sheet) use($ordersExports) {
                    $sheet->loadView('admin.store.order.index_xls')
                        ->with('orders', $ordersExports);
                });
            })->export('xlsx');
        }

        $orders = $orderObj->paginate(50);

        return view('admin.store.order.index', [
            'orders' => $orders,
            'has_filter' => strlen($filter_user_name) > 0 || strlen($filter_item_name) > 0 || strlen($filter_status) > 0,
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
    public function destroy($id)
    {
        //
    }
}
