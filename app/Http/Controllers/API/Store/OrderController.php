<?php

namespace App\Http\Controllers\API\Store;

use App\Http\Controllers\API\APIController;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\StoreItem;
use App\StoreOrder;
use App\UserPointLog;
use App\UserMoneyLog;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @SWG\Tag(name="Store Order", description="商城订单")
 */
class OrderController extends APIController
{

    /**
     * @SWG\Post(
     *     path="/store/orders",
     *     tags={"Store Order"},
     *     summary="新建订单",
     *     @SWG\Parameter(name="item_id", in="formData", required=true, type="integer"),
     *     @SWG\Parameter(name="amount", in="formData", required=true, type="integer"),
     *     @SWG\Parameter(name="shipping_address", in="formData", required=false, type="string"),
     *     @SWG\Response(response="200", description="服务员所有商城订单")
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request) {
        $this->validate($request, [
            'item_id' => 'exists:store_items,id',
            'amount' => 'required|integer|min:1',
        ], [
            'item_id.*' => '601|商品未找到',
            'amount.*' => '602|商品数量不正确',
        ]);

        $user = $request->user;
        $item_id = $request->input('item_id');
        $amount = $request->input('amount');
        $shipping_address = $request->input('shipping_address');
        $contact_name = $request->input('contact_name');
        $contact_phone = $request->input('contact_phone');
        $item = StoreItem::find($item_id);

        if ($item->is_virtual == false && empty(trim($shipping_address))) {
            return new JsonResponse($this->buildErrorResponse('606|非虚拟物品需要填写收货地址'), 400);
        }

        $total_cost_point = $item->price_point * $amount;
        $total_cost_money = $item->price_money * $amount;
        if ($request->user->point_balance <  $total_cost_point) {
            return new JsonResponse($this->buildErrorResponse('603|积分不足'), 400);
        }
        if ($request->user->money_balance <  $total_cost_money) {
            return new JsonResponse($this->buildErrorResponse('603|余额不足'), 400);
        }

        if ($item->status != "in_stock") {
            return new JsonResponse($this->buildErrorResponse('604|该商品不可买'), 400);
        }
        if ($item->stock < $amount) {
            return new JsonResponse($this->buildErrorResponse('605|该商品数量不够'), 400);
        }

        $order = StoreOrder::create([
            'item_id' => $item_id,
            'amount' => $amount,
            'user_id' => $user->id,
            'shipping_address' => $shipping_address,
            'contact_name' => $contact_name,
            'contact_phone' => $contact_phone,
            'remarks' => null,
            'status' => 'created',
            'type' => 'exchange',
            'pay_way' => 'balance',
        ]);

        if ($order->id != 0) {
            $item->stock -= $amount;
            $item->save();

            $user->point_balance -= $total_cost_point;
            $user->money_balance -= $total_cost_money;
            $user->save();

            if ($total_cost_point > 0) {
                UserPointLog::create([
                    'type' => 'store_order_use',
                    'amount' => -$total_cost_point,
                    'user_id' => $user->id,
                    'store_order_id' => $order->id,
                    'comment' => 'Order ' + $order->id,
                ]);
            }

            if ($total_cost_money > 0) {
                UserMoneyLog::create([
                    'type' => 'store_order_use',
                    'amount' => -$total_cost_money,
                    'user_id' => $user->id,
                    'store_order_id' => $order->id,
                    'comment' => 'Order ' + $order->id,
                ]);
            }

            $order->load('item');
            return new JsonResponse($order->toArray());
        } else {
            return new JsonResponse($this->buildErrorResponse('600|订单创建失败'), 400);
        }
    }

    /**
     * @SWG\Get(
     *     path="/store/users/{user}/orders",
     *     tags={"Store Order"},
     *     summary="获取当前服务员所有商城订单",
     *     @SWG\Parameter(name="user", in="path", required=false, type="string"),
     *     @SWG\Response(response="200", description="服务员所有商城订单")
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function listByUser($user_id, Request $request) {
        if ($user_id == "@me") {
            $user_id = $request->user->id;
        }

        $orders = StoreOrder::with('item')
            ->where('user_id', $user_id)
            ->where('type', 'exchange')
            ->orderBy('id', 'desc')
            ->get();

        return new JsonResponse($orders->toArray());
    }
}
