<?php

namespace App\Http\Controllers\API\Store;

use App\Http\Controllers\API\APIController;
use App\StoreItem;

use App\Http\Requests;
use Illuminate\Http\JsonResponse;

/**
 * @SWG\Tag(name="Store Item", description="商城商品")
 */
class ItemController extends APIController
{

    /**
     * @SWG\Get(
     *     path="/store/items",
     *     tags={"Store Item"},
     *     summary="获取所有商城商品",
     *     @SWG\Response(response="200", description="商城商品获取成功"),
     *     @SWG\Response(response="500", description="服务器内部错误"),
     * )
     */
    public function index() {
        $items = StoreItem::where('status', 'in_stock')
            ->where('type', 'exchange')
            ->orderBy('id', 'desc')
            ->get();

        /* Backward capability, to support old mobile client, which need price instead of price_point and price_money */
        $items = $items->each(function ($item, $key) {
            if ($item->price_money == 0) {
                $item->price = $item->price_point;
            }
        });

        return new JsonResponse($items->toArray());
    }
}
