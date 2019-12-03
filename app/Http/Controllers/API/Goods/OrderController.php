<?php

namespace App\Http\Controllers\API\Goods;

use App\Http\Controllers\API\APIController;
use App\Pay;
use App\PayLog;
use App\StoreItem;
use App\StoreOrder;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Helpers\Alipay;
use Log;
use DB;

/**
 * @SWG\Tag(name="Goods Order", description="采购订单")
 */
class OrderController extends APIController
{
    /**
     * @SWG\Post(
     *     path="/goods/orders",
     *     tags={"Goods Order"},
     *     summary="新建采购订单",
     *     @SWG\Parameter(name="item_id", in="formData", required=true, type="integer"),
     *     @SWG\Parameter(name="amount", in="formData", required=true, type="integer"),
     *     @SWG\Parameter(name="contact_name", in="formData", required=true, type="string"),
     *     @SWG\Parameter(name="contact_phone", in="formData", required=true, type="string"),
     *     @SWG\Parameter(name="shipping_address", in="formData", required=true, type="string"),
     *     @SWG\Response(response="200", description="店长所有采购订单")
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request) {
        $this->validate($request, [
            'item_id' => 'required|exists:store_items,id',
            'amount' => 'required|integer|min:1',
            'contact_name' => 'required',
            'contact_phone' => 'required',
            'shipping_address' => 'required|min:5',
        ], [
            'item_id.required' => '601|商品ID必填',
            'item_id.exists' => '601|商品未找到',
            'amount.*' => '602|商品数量不正确',
            'contact_name.required' => '602|收货人必填',
            'contact_phone.required' => '602|联系电话必填',
            'shipping_address.required' => '602|联系地址必填',
            'shipping_address.min' => '602|联系地址最少为5个字符',
        ]);

        $user = $request->user;
        $user->load('shop');
        $item_id = $request->input('item_id');
        $amount = $request->input('amount');
        $contact_name = $request->input('contact_name');
        $contact_phone = $request->input('contact_phone');
        $shipping_address = $request->input('shipping_address');

        $item = StoreItem::find($item_id);

        if ($user->id != $user->shop->owner_id) {
            return new JsonResponse($this->buildErrorResponse('604|您没有权限下采购订单'), 400);
        }

        if (!$item) {
            return new JsonResponse($this->buildErrorResponse('604|该商品不存在'), 400);
        }

        if ($item->status != "in_stock") {
            return new JsonResponse($this->buildErrorResponse('604|该商品不可买'), 400);
        }
        if ($item->type != 'purchase') {
            return new JsonResponse($this->buildErrorResponse('604|该商品不可买'), 400);
        }
        if ($item->stock < $amount) {
            return new JsonResponse($this->buildErrorResponse('605|该商品数量不足'), 400);
        }

        $order = StoreOrder::create([
            'user_id' => $user->id,
            'item_id' => $item_id,
            'amount' => $amount,
            'contact_name' => $contact_name,
            'contact_phone' => $contact_phone,
            'shipping_address' => $shipping_address,
            'money' => $item->price_money * $amount,
            'salesman_id' => $user->shop && $user->shop->salesman_id ? $user->shop->salesman_id : 0,
            'distributor_id' => $user->shop && $user->shop->distributor_id ? $user->shop->distributor_id : 0,
            'shop_id' => $user->shop_id ? $user->shop_id : 0,
            'status' => 'established',
            'remarks' => null,
            'type' => 'purchase',
            'pay_way' => null,
            'is_pay' => false,
            'is_checked' => false,
        ]);

        if ($order->id != 0) {
            $item->stock -= $amount;
            $item->save();

            // net消息通知
            $date_time = explode(' ', $order->created_at);
            $param = [
                'ShopName' => $user->shop->name,
                'CreatedS' => $date_time[0],
                'CreatedE' => $date_time[1],
                'Name' => $item->name,
                'Amount' => $order->amount,
                'Money' => $order->money / 100,
                'CounselorId' => $order->salesman_id ? $order->salesman_id : '',
                'Title' => '订单下单通知',
                'Type' => 2,
            ];
            order_notification($param);

            $order->load('item');
            return new JsonResponse($order);
        } else {
            return new JsonResponse($this->buildErrorResponse('600|订单创建失败'), 400);
        }
    }

    /**
     * 接收支付宝支付结果
     * @param Request $request
     * @return JsonResponse
     */
    public function notifications(Request $request)
    {
        $order_id = $request->input('out_trade_no');
        $seller_id = $request->input('seller_id');
        $seller_email = $request->input('seller_email');
        $buyer_id = $request->input('buyer_id');
        $buyer_logon_id = $request->input('buyer_logon_id');
        $buyer_pay_amount = $request->input('buyer_pay_amount');
        $gmt_payment = $request->input('gmt_payment');
        $notify_time = $request->input('notify_time');
        $trade_no = $request->input('trade_no');
        $trade_status = $request->input('trade_status');

        //处理业务，并从$_POST中提取需要的参数内容
        if($trade_status == 'TRADE_SUCCESS') //处理交易完成或者支付成功的通知
        {
            //此处编写回调处理逻辑
            $order = StoreOrder::where('is_pay', false)
                ->where('pay_way', null)
                ->where('status', 'established')
                ->find($order_id);

            if ($order) {
                if ($buyer_pay_amount == $order->money / 100) {
                    $order->is_pay = true;
                    $order->pay_way = 'alipay';
                    if ($order->save()) {
                        PayLog::create([
                            'order_id' => $order_id,
                            'seller_id' => $seller_id,
                            'seller_email' => $seller_email,
                            'buyer_id' => $buyer_id,
                            'buyer_logon_id' => $buyer_logon_id,
                            'buyer_pay_amount' => $buyer_pay_amount * 100,
                            'gmt_payment' => $gmt_payment,
                            'notify_time' => $notify_time,
                            'trade_no' => $trade_no,
                            'trade_status' => $trade_status,
                            'pay_way' => 'alipay'
                        ]);

                        Log::useFiles(storage_path('logs/alipay.log'), 'debug');
                        Log::info('ALIPAY_ORDER_INFO:', [
                            'ALIPAY_BACK' => $request->all(),
                            'ORDER_INFO' => $order->toArray(),
                        ]);
                        die('success');//响应success表示业务处理成功，告知支付宝无需在异步通知
                    }
                }
            }
        }
    }

    /**
     * 根据支付方式生成密钥
     */
    public function generate(Request $request)
    {
        $this->validate($request, [
            'order_id' => 'required|integer|exists:store_orders,id',
            'pay_way_id' => 'required|integer|exists:pays,id'
        ], [
            'order_id.required' => '订单ID必传！',
            'order_id.integer' => '订单ID必须为整数！',
            'order_id.exists' => '此订单不存在！',
            'pay_way_id.required' => '支付方式ID必传！',
            'pay_way_id.integer' => '支付方式ID必须为整数！',
            'pay_way_id.exists' => '此支付方式不存在！',
        ]);

        $order_id = $request->input('order_id');
        $pay_way_id = $request->input('pay_way_id');
        $order = StoreOrder::with('item')->find($order_id);
        $pay = Pay::find($pay_way_id);

        if ($pay->status != '1') {
            return new JsonResponse($this->buildErrorResponse('400|此支付方式暂不可用'), 400);
        } elseif ($pay->pay_way == 'alipay') {
            $alipay_param = get_alipay_param($order->item->name, $order->item->description, $order_id, $order->money);
            return new JsonResponse($alipay_param);
        }

        return new JsonResponse($this->buildErrorResponse('400|暂未开通此支付方式'), 400);
    }

    /**
     * @SWG\Get(
     *     path="/goods/orders",
     *     tags={"Goods Order"},
     *     summary="获取所有采购订单",
     *     @SWG\Parameter(name="status", in="query", required=false, type="string"),
     *     @SWG\Parameter(name="page", in="query", required=false, type="integer"),
     *     @SWG\Parameter(name="pageSize", in="query", required=false, type="integer"),
     *     @SWG\Response(response="200", description="采购商品获取成功"),
     *     @SWG\Response(response="500", description="服务器内部错误"),
     * )
     */
    public function orderList(Request $request) {
        $this->validate($request, [
            'status' => 'in:created,shipped,canceled,drawback,finished',
            'page' => 'integer|min:1',
            'pageSize' => 'integer|min:1',
        ], [
            'status.in' => '订单状态值有误',
            'page.*' => '页码必须为大于等于1的整数',
            'pageSize.in' => '数量必须为大于等于1的整数',
        ]);

        $filter_status = $request->input('status');
        $user = $request->user;
        $orders = StoreOrder::with('item')
            ->status($filter_status)
            ->where('user_id', $user->id)
            ->where('type', 'purchase')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->simplePaginate($request->input('pageSize'));

        $orders = $orders->each(function ($item, $key) {
            if ($item->item && $item->item->photo_url) {
                $item->item->photo = config('custom.app_url').$item->item->photo_url;
            }
            unset($item->item->photo_url);
            $item->show_pay_button = false;
            $item->show_cancel_button = false;
            if (!$item->is_pay && $item->status != 'canceled') {
                $item->show_pay_button = true;
            }

            if ($item->status == 'established' && $item->is_checked == 0) {
                $item->show_cancel_button = true;
            }
        });

        return new JsonResponse($orders);
    }

    /**
     * @SWG\Get(
     *     path="/goods/orders/show",
     *     tags={"Goods Order"},
     *     summary="获取采购订单详情",
     *     @SWG\Parameter(name="order_id", in="query", required=true, type="integer"),
     *     @SWG\Response(response="200", description="采购商品获取成功"),
     *     @SWG\Response(response="500", description="服务器内部错误"),
     * )
     */
    public function show(Request $request) {
        $this->validate($request, [
            'order_id' => 'required|integer|exists:store_orders,id'
        ], [
            'order_id.required' => '订单ID不能为空',
            'order_id.integer' => '订单ID必须为整数',
            'order_id.exists' => '该订单不存在',
        ]);

        $order = StoreOrder::with('item')->find($request->order_id);

        if (!$order) {
            return new JsonResponse($this->buildErrorResponse('604|该订单不存在'), 400);
        }

        if ($order->user_id != $request->user->id) {
            return new JsonResponse($this->buildErrorResponse('604|您无权查看此订单'), 400);
        }

        if ($order->item->photo_url !== null) {
            $order->item->photo_url = config('custom.app_url').$order->item->photo_url;
        }

        $order->show_pay_button = false;
        $order->show_cancel_button = false;
        if (!$order->is_pay && $order->status != 'canceled') {
            $order->show_pay_button = true;
        }

        if ($order->status == 'established' && $order->is_checked == 0) {
            $order->show_cancel_button = true;
        }

        return new JsonResponse($order);
    }

    /**
     * @SWG\Post(
     *     path="/goods/orders/cancel",
     *     tags={"Goods Order"},
     *     summary="取消采购订单",
     *     @SWG\Parameter(name="order_id", in="formData", required=true, type="integer"),
     *     @SWG\Response(response="200", description="服务员所有采购订单")
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function cancel(Request $request)
    {
        $this->validate($request, [
            'order_id' => 'required|integer|exists:store_orders,id'
        ], [
            'order_id.required' => '订单ID必传',
            'order_id.integer' => '订单ID必须为整数',
            'order_id.exists' => '此订单不存在',
        ]);
        $user = $request->user;
        $user = 1;

        $order = StoreOrder::find($request->order_id);
        dd($order);
        // if ($order->user_id !== $user->id) {
        //     return new JsonResponse($this->buildErrorResponse('403|没有权限取消该订单'), 400);
        // }

        if ($order->status == 'drawback') {
            return new JsonResponse($this->buildErrorResponse('403|该订单正在审核中'), 400);
        }

        if ($order->status == 'canceled') {
            return new JsonResponse($this->buildErrorResponse('403|该订单已取消，不能重复取消该订单'), 400);
        }

        if (in_array($order->status, ['shipped', 'finished'])) {
            return new JsonResponse($this->buildErrorResponse('403|已发货和已完成的订单不能取消'), 400);
        }

        if (in_array($order->status, ['established']) && $order->is_pay == false) {
            $order->status = 'canceled';
            $result = $order->save();
            if (!$result) {
                return new JsonResponse($this->buildErrorResponse('403|取消订单失败，请重试'), 400);
            }
        } elseif ($order->is_pay == true) {
            $order->status = 'drawback';
            $result = $order->save();
            if (!$result) {
                return new JsonResponse($this->buildErrorResponse('403|取消订单失败，请重试'), 400);
            }
        } else {
            if ($order->status == 'created') {
            $order->status = 'canceled';
            $result = $order->save();
                if (!$result) {
                    return new JsonResponse($this->buildErrorResponse('403|取消订单失败，请重试'), 400);
                }
            } else{
            return new JsonResponse($this->buildErrorResponse('403|订单异常，请重试'), 400);
            }
        }

        $order = StoreOrder::with('item')->find($request->order_id);
        if ($order->item->photo_url) {
            $order->item->photo_url = config('custom.app_url').$order->item->photo_url;
        }

        return new JsonResponse($order);
    }
    public function canceled(Request $request)
    {
        $this->validate($request, [
            'order_id' => 'required|integer|exists:store_orders,id'
        ], [
            'order_id.required' => '订单ID必传',
            'order_id.integer' => '订单ID必须为整数',
            'order_id.exists' => '此订单不存在',
        ]);
        // $user = $request->user;

        $order = StoreOrder::find($request->order_id);
        // dd($order);
        // if ($order->user_id !== $user->id) {
        //     return new JsonResponse($this->buildErrorResponse('403|没有权限取消该订单'), 400);
        // }
        if ($order->status == 'created') {
        $order->status = 'canceled';
        $result = $order->save();
            if ($result) {
                //查询下单商品数量
                $item_id = $order->item_id;//商品id
                $amount = $order->amount;//下单数量
                $user_id = $order->user_id;
                // dd($user_id);
                $goods = DB::table('store_items as r')
                        ->where('r.id','=',$item_id)
                        ->get();
                $goods = $goods['0'];
                // 查询下单商品的信息
                $price_money = $goods->price_money; //价格
                $price_point = $goods->price_point; //积分
                $stock = $goods->stock;             //库存
                $stock = $amount + $stock;
                // 返还库存
                $update_fields = ['stock'=>$stock];
                $Updated = DB::table('store_items as r')
                        ->where('r.id','=',$item_id)
                        ->update($update_fields);
                // 查询用户积分以及钱包
                $users = DB::table('users as u')
                        ->where('u.id','=',$user_id)
                        ->get();
                $user = $users['0'];
                $money_balance = $user->money_balance;
                $point_balance = $user->point_balance;
                $user_money_balance = $money_balance + ($price_money*$amount);
                $user_point_balance = $point_balance + ($price_point*$amount);

                $user_fields = ['money_balance'=>$user_money_balance,'point_balance'=>$user_point_balance];
                //更新用户积分钱包
                $UserUpdated = DB::table('users as u')
                        ->where('u.id','=',$user_id)
                        ->update($user_fields);
                //錢包提現記錄
                if ($price_money>0) {
                    $amounts=$price_money*$amount;
                    $type='store_order_use';
                    $user_id = $user->id;
                    $showtime=date("Y-m-d H:i:s");
                    DB::table('user_money_logs')->insert(
                        ['type' => 'store_order_use', 'amount' => $amounts ,'user_id' => $user_id, 'created_at' => $showtime]);
                }
                $order = StoreOrder::with('item')->find($request->order_id);
                if ($order->item->photo_url) {
                    $order->item->photo_url = config('custom.app_url').$order->item->photo_url;
                }
                return new JsonResponse(['code' => 200, 'message' => '取消订单成功.']);
                
            }
        } else{
        return new JsonResponse(['code' => 403, 'message' => '订单异常，请重试']);
        }

    }
}
