<?php

namespace App\Http\Controllers\Net;

use App\Area;
use App\Category;
use App\Shop;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DataController extends NetController
{
    /**
     * Net同步终端数据接口
     * @param string $created_at 添加时间
     * @param string $updated_at 更新时间
     */
    public function link(Request $request)
    {
        $this->validate($request, [
            'startTime' => 'required|date',
            'endTime' => 'required|date',
        ], [
            'startTime.required' => '开始时间不能为空',
            'startTime.date' => '开始时间必须为日期格式',
            'endTime.required' => '结束时间不能为空',
            'endTime.date' => '结束时间必须为日期格式',
        ]);

        $createdShops = Shop::with('category')
            ->whereBetween('created_at', [$request->startTime, $request->endTime])
            ->whereRaw('created_at = updated_at')
            ->get();

        $createdShops = $createdShops->each(function ($item, $key) {
            $item->data_type = 'add';
        });

        $updatedShops = Shop::with('category')
            ->whereBetween('updated_at', [$request->startTime, $request->endTime])
            ->whereRaw('created_at < updated_at')
            ->orderBy('id', 'desc')
            ->get();

        $updatedShops = $updatedShops->each(function ($item, $key) {
            $item->data_type = 'update';
        });

        $deletedData = Shop::with('category')
            ->withTrashed()
            ->whereBetween('deleted_at', [$request->startTime, $request->endTime])
            ->orderBy('id', 'desc')
            ->get();

        $deletedData = $deletedData->each(function ($item, $key) {
            $item->data_type = 'delete';
        });

        $shopsData = array_merge($createdShops->toArray(), $updatedShops->toArray(), $deletedData->toArray());

        return new JsonResponse($shopsData);
    }

    /**
     * Net同步地区数据接口
     */
    public function areas()
    {
        $areaObj = Area::get();
        return new JsonResponse($areaObj->toArray());
    }

    /**
     * 餐饮类别数据
     */
    public function categories()
    {
        $categories = Category::where('level', 1)->get();
        return new JsonResponse($categories);
    }
}
