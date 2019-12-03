<?php

namespace App\Http\Controllers\Net\Goods;

use App\Http\Controllers\Net\NetController;
use App\Salesman;
use App\StoreOrder;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends NetController
{
    private $order;

    public function __construct(StoreOrder $order)
    {
        $this->order = $order;
    }

    /**
     * 营销员所有订单
     * @param Request $request
     * @return array
     */
    public function salesOrder(Request $request)
    {
        $this->validateSales($request);

        $salesman_ids = explode(',', $request->input('salesman_id'));
        $shop_id = $request->input('shop_id');
        $salesman_orders = [];
        foreach ($salesman_ids as $salesman_id) {
            $orders = $this->order->with('shop')
                ->salesman($salesman_id)
                ->shop($shop_id)
                ->where('type', 'purchase')
                ->orderBy('id', 'desc')
                ->paginate($request->input('pageSize', 20));

            $ordersData = $orders->toArray();
            $salesman_orders['total'] = $ordersData['total'];
            $salesman_orders['data'] = $ordersData['data'];
        }
        return $this->jsonReturn(200, '查询成功！', $salesman_orders);
    }

    private function validateSales($request)
    {
        Validator::extendImplicit('exists_id', function($attribute, $value, $parameters, $validator) {
            if ($value) {
                $salesman_ids = explode(',', $value);
                foreach ($salesman_ids as $salesman_id) {
                    $salesman = Salesman::find($salesman_id);
                    if (!$salesman) {
                        return false;
                    }
                }
            }

            return true;
        });

        $this->validate($request, [
            'salesman_id'   => 'required_without:shop_id|exists_id',
            'shop_id'   => 'required_without:salesman_id|integer|exists:shops,id',
        ], [
            'salesman_id.exists_id' => '数据中有营销员ID不存在',
            'shop_id.integer' => '终端ID必须为整数',
            'shop_id.exists' => '此终端不存在',
        ]);
    }
}
