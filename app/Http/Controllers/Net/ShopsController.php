<?php

namespace App\Http\Controllers\Net;

use App\Shop;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShopsController extends NetController
{
    protected $shop;
    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
    }

    public function index()
    {
        $shopObj = $this->shop->with('category')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $shopData = $shopObj->toArray();
        return new JsonResponse($shopData['data']);
    }
	
	/**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!$request->id) {
            $this->validateForm($request);
            $request->merge(['source' => 1]);
            $shop = $this->shop->create($request->except('id'));
            if ($shop->id != 0) {
                return $this->jsonReturn(200, '终端创建成功', ['id' => $shop->id]);
            }

            return $this->jsonReturn(400, '终端创建失败');

        } else {
            $this->validateForm($request, $request->id);

            $sn = $this->shop->withTrashed()->find($request->id);
            $sn->restore();  //更改数据启用禁用状态(BD后台同步数据是否把终端启用.)
            $shop = $sn->update($request->except(['id', 'net_shop_id']));     
            if ($shop) {
                return $this->jsonReturn(200, '终端修改成功');
            }

            return $this->jsonReturn(400, '终端修改失败');
        }
       
    }

    /**
     * 判断终端名称是否存在
     *
     * @param  string $shop_name
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function check(Request $request)
    {
        $this->validate($request, [
            'shop_name' => 'required',
        ], [
            'shop_name.required' => 'shop_name 不能为空。',
        ]);

        $message = '';
        $code = null;
        $distributor = null;
        $shop = Shop::where('name', $request->input('shop_name'))->count();

        if ($shop) {
            $code = 400;
            $message .= '终端已存在';
        } else {
            $code = 200;
            $message .= '终端不存在';
        }

        return $this->jsonReturn($code,$message);
    }

    public function show(Request $request)
    {
        $this->validateShopsId($request);


        $shop_ids = explode(',', $request->input('shop_id'));
        $shops = Shop::with(['area', 'distributor', 'category', 'salesman'])
                    ->whereIn('id', $shop_ids)
                    ->get();

        return $this->jsonReturn(200, '查询成功', $shops);
    }

    /**
     * 验证多个终端ID
     */
    protected function validateShopsId(Request $request)
    {
        Validator::extendImplicit('exists_shop_id', function($attribute, $value, $parameters, $validator) {
            $shop_ids = explode(',', $value);
            foreach ($shop_ids as $shop_id) {
                $shop = Shop::find($shop_id);
                if (!$shop) {
                    return false;
                }
            }
            return true;
        });

        $this->validate($request, [
            'shop_id' => 'required|exists_shop_id',
        ], [
            'shop_id.exists_id' => '数据中有终端ID不存在',
        ]);
    }

	/**
     * Validate form for store and update
     *
     * @param Request $request
     */
    protected function validateForm(Request $request, $id = null)
    {
        if ($id != null) {
            $this->validate($request, [
                'id'             => 'required|integer|exists:shops',
                'name'           => 'required|max:15|min:2|unique:shops,name,'.$id,
                'distributor_id' => 'exists:distributors,id',
                'area_id'        => 'required|exists:areas,id',
                'address'        => 'required|min:2|max:100',
                'salesman_id'    => 'required|exists:salesmen,id',
                'contact_person' => 'required',
                'contact_phone'  => 'required',
                'category_id'    => 'required|exists:categories,id',
                'per_consume'    => 'numeric',
                'logo'           => 'required',
            ], [
                'net_shop_id.required'  => '终端ID不能为空。',
                'net_shop_id.integer'   => '终端ID必须为整数。',
                'net_shop_id.unique'    => '终端ID已存在。',
                'name.required'         => '终端名称不能为空。',
                'name.max'              => '终端名称必须为2-15位中英文字符',
                'name.min'              => '终端名称必须为2-15位中英文字符',
                'name.unique' 			=> '终端名称已存在，请更换名称终端',
                'distributor_id.exists' => '经销商未找到',
                'area_id.required'      => '地区不能为空',
                'area_id.exists'        => '该地区未找到',
                'address.required'      => '地址不能为空,请填入2-100个字符',
                'address.min'           => '地址不能为空,请填入2-100个字符',
                'address.max'           => '地址不能为空,请填入2-100个字符',
                'salesman_id.required'  => '营销员ID不能为空',
                'salesman_id.exists'    => '营销员未找到',
                'contact_person.required' => '联系人不能为空',
                'contact_phone.required' => '联系方式不能为空',
                'category_id.required'  => '餐饮类型不能为空',
                'category_id.exists'    => '餐饮类型未找到',
                'per_consume.numeric'   => '人均消费为数值类型',
                'logo.required'         => '终端展示图不能为空',
            ]);
        } else {
            $this->validate($request, [
                'net_shop_id'    => 'required|integer|unique:shops,net_shop_id',
                'name'           => 'required|max:15|min:2|unique:shops,name',
                'distributor_id' => 'exists:distributors,id',
                'area_id'        => 'required|exists:areas,id',
                'address'        => 'required|min:2|max:100',
                'salesman_id'    => 'required|exists:salesmen,id',
                'contact_person' => 'required',
                'contact_phone'  => 'required',
                'category_id'    => 'required|exists:categories,id',
                'per_consume'    => 'numeric',
                'logo'           => 'required',
            ], [
                'net_shop_id.required'  => '终端ID不能为空。',
                'net_shop_id.integer'   => '终端ID必须为整数。',
                'net_shop_id.unique'    => '终端ID已存在。',
                'name.required'         => '终端名称不能为空。',
                'name.max'              => '终端名称必须为2-15位中英文字符',
                'name.min'              => '终端名称必须为2-15位中英文字符',
                'name.unique'           => '终端名称已存在，请更换终端名称',
                'distributor_id.exists' => '经销商未找到',
                'area_id.required'      => '地区不能为空',
                'area_id.exists'        => '该地区未找到',
                'address.required'      => '地址不能为空,请填入2-100个字符',
                'address.min'           => '地址不能为空,请填入2-100个字符',
                'address.max'           => '地址不能为空,请填入2-100个字符',
                'salesman_id.required'  => '营销员ID不能为空',
                'salesman_id.exists'    => '营销员未找到',
                'contact_person.required' => '联系人不能为空',
                'contact_phone.required'=> '联系方式不能为空',
                'category_id.required'  => '餐饮类型不能为空',
                'category_id.exists'    => '餐饮类型未找到',
                'per_consume.numeric'   => '人均消费为数值类型',
                'logo.required'         => '终端展示图不能为空',
            ]);
        }

    }
}
