<?php

namespace App\Http\Controllers\API;

use App\Shop;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @SWG\Tag(name="Shop", description="终端")
 */
class ShopController extends APIController
{
    /**
     * @SWG\Get(
     *     path="/shops",
     *     tags={"Shop"},
     *     summary="获取十个终端,或者搜索终端",
     *     @SWG\Parameter(name="q", in="query", required=false, type="string"),
     *     @SWG\Response(response="200", description="搜索餐厅")
     *     )
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Shop $shop, Request $request) {
        $filter_keyword = $request->input('q');

        $shopsObj = $shop->keyword($filter_keyword)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($request->input('pageSize', 10));

        $shops = $shopsObj->toArray();
        return new JsonResponse($shops['data']);
    }

    /**
     * @SWG\Post(
     *     path="/shops",
     *     tags={"Shop"},
     *     summary="添加新终端",
     *     @SWG\Parameter(name="name", in="formData", required=true, type="string"),
     *     @SWG\Parameter(name="distributor_id", in="formData", required=false, type="string"),
     *     @SWG\Parameter(name="area_id", in="formData", required=true, type="string"),
     *     @SWG\Parameter(name="address", in="formData", required=true, type="string"),
     *     @SWG\Response(response="200", description="添加新餐厅")
     *     )
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Shop $shop, Request $request) {

        // 客户端不需要此功能【废弃】
        return new JsonResponse($this->buildErrorResponse('600|找不到餐厅，请重新选择'), 500);

        /*$this->validate($request, [
            'name' => 'required|max:15|min:1|unique:shops,name',
            'distributor_id' => 'exists:distributors,id',
            'area_id' => 'required|exists:areas,id',
            'address' => 'max:100|unique:shops,address'
        ], [
            'name.required' => '601|名称为必填项,请填入1-15位中英文字符',
            'name.max' => '601|名称为必填项,请填入1-15位中英文字符',
            'name.min' => '601|名称为必填项,请填入1-15位中英文字符',
            'name.unique' => '601|名称已存在，请更换名称',
            'distributor_id.exists' => '602|经销商未找到',
            'area_id.required' => '603|地区为必填项',
            'area_id.exists' => '604|该地址未找到',
            'address.max' => '605|地址最多100个字符',
            'address.unique' => '605|地址已存在，请更换地址'
        ]);

        $shop_info = $request->only(['name', 'distributor_id', 'area_id', 'address']);
        $shop_info['level'] = 'D';
        $shop = $shop->create($shop_info);

        if ($shop->id != 0) {
            return new JsonResponse($shop);
        }

        return new JsonResponse($this->buildErrorResponse('600|终端创建失败'), 500);*/
    }
}
