<?php

namespace App\Http\Controllers\API\Goods;

use App\Http\Controllers\API\APIController;
use Symfony\Component\HttpFoundation\Request;
use App\StoreItem;
use Illuminate\Http\JsonResponse;

/**
 * @SWG\Tag(name="Extra Item", description="采购商品")
 */
class ItemController extends APIController
{

    /**
     * @SWG\Get(
     *     path="/goods/items",
     *     tags={"Goods Item"},
     *     summary="获取所有采购商品",
     *     @SWG\Parameter(name="page", in="query", required=false, type="integer"),
     *     @SWG\Parameter(name="pageSize", in="query", required=false, type="integer"),
     *     @SWG\Response(response="200", description="采购商品获取成功"),
     *     @SWG\Response(response="500", description="服务器内部错误"),
     * )
     */
    public function index(Request $request)
    {
        $columns = ['id', 'name', 'photo_url', 'stock', 'price_money'];
        $items = StoreItem::where('status', 'in_stock')
            ->where('type', 'purchase')
            ->orderBy('id', 'desc')
            ->simplePaginate($request->input('pageSize'), $columns);

        $items = $items->each(function ($item, $key) {
            if ($item->photo_url !== null) {
                $item->photo_url = config('custom.app_url').$item->photo_url;
            }
        });

        return new JsonResponse($items->toArray());
    }
}
