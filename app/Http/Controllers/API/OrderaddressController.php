<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Orderaddress;
class OrderaddressController extends APIController
{
    public function __construct(Orderaddress $orderaddress)
    {
        $this->orderaddress = $orderaddress;
    }
    /**
     * @SWG\Get(
     *     path="/orderaddress",
     *     tags={"Orderaddress"},
     *     summary="获取所有地址",
     *     @SWG\Response(response="200", description="设置获取成功"),
     *     @SWG\Response(response="500", description="服务器内部错误"),
     * )
     */
    public function index(Request $request) {
         // 1. validate input
        $this->validate($request, [
            'user_id' => 'required|exists:users,id',
        ], [
            'user_id.*' => '501|反馈内容不能为空',
        ]);
        $uid = $request->user_id;
        $newsList = Orderaddress::orderBy('id', 'desc')
            ->where('user_id','=', $uid)->get();
        
        return new JsonResponse($newsList->toArray());
    }

    /**
     * @SWG\Post(
     *     path="/orderaddress",
     *     tags={"Orderaddress"},
     *     summary="添加新终端",
     *     @SWG\Parameter(name="contact_name", in="formData", required=true, type="string"),
     *     @SWG\Parameter(name="contact_phone", in="formData", required=false, type="string"),
     *     @SWG\Parameter(name="contact_address", in="formData", required=true, type="string"),
     *     @SWG\Parameter(name="user_id", in="formData", required=true, type="integer"),
     *     @SWG\Response(response="200", description="添加新地址")
     *     )
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Orderaddress $orderaddress, Request $request) {

        $this->validate($request, [
            'contact_name' => 'required|max:15|min:1',
            'contact_phone' => 'required',
            'contact_address' => 'required|max:100',
            'user_id' => 'required'
        ], [
            'contact_name.required' => '601|名称为必填项,请填入1-15位中英文字符',
            'contact_name.max' => '601|名称为必填项,请填入1-15位中英文字符',
            'contact_name.min' => '601|名称为必填项,请填入1-15位中英文字符',
            'contact_phone.required' => '602|电话为必填项',
            'contact_address.required' => '603|地区为必填项',
            'contact_address.max' => '604|地址最多100个字符',
            'user_id' => '605|用户id不为空'
        ]);

        $address_info = $request->only(['contact_name', 'contact_phone', 'contact_address', 'user_id']);

        $orderaddress = $orderaddress->insert($address_info);

        if ($orderaddress) {
             return new JsonResponse($this->buildErrorResponse('添加地址成功'),200);
        }

        return new JsonResponse($this->buildErrorResponse('600|终端创建失败'), 500);
    }

    /**
     * @SWG\Post(
     *     path="/orderaddress",
     *     tags={"Orderaddress"},
     *     summary="修改地址",
     *     @SWG\Parameter(name="contact_name", in="formData", required=false, type="string"),
     *     @SWG\Parameter(name="contact_phone", in="formData", required=false, type="string"),
     *     @SWG\Parameter(name="contact_address", in="formData", required=false, type="string"),
     *     @SWG\Parameter(name="user_id", in="formData", required=false, type="integer"),
     *     @SWG\Response(response="200", description="用户数据",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(type="string", property="id"),
     *              @SWG\Property(type="string", property="contact_name"),
     *              @SWG\Property(type="string", property="contact_phone"),
     *              @SWG\Property(type="string", property="contact_address"),
     *              @SWG\Property(type="integer", property="user_id"),
     *              @SWG\Property(type="string", property="deleted_at"),
     *              @SWG\Property(type="string", property="created_at"),
     *              @SWG\Property(type="string", property="updated_at"),
     *              @SWG\Property(type="string", property="token"),
     *          )
     *     )
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Orderaddress $orderaddress,Request $request) {

        $this->validate($request, [
            'contact_name' => 'required|max:15|min:1',
            'contact_phone' => 'required',
            'contact_address' => 'required|max:100',
            'id' => 'required'
        ], [
            'contact_name.required' => '601|名称为必填项,请填入1-15位中英文字符',
            'contact_name.max' => '601|名称为必填项,请填入1-15位中英文字符',
            'contact_name.min' => '601|名称为必填项,请填入1-15位中英文字符',
            'contact_phone.required' => '602|电话为必填项',
            'contact_address.required' => '603|地区为必填项',
            'contact_address.max' => '604|地址最多100个字符',
            'id' => '605|用户id不为空'
        ]);

        $update_fields = ['contact_name'=>$request->contact_name, 'contact_phone'=>$request->contact_phone, 'contact_address'=>$request->contact_address, 'id'=>$request->id];
        $isUpdated = $orderaddress::where('id','=',1)->update($update_fields);
        if ($isUpdated) {
            return new JsonResponse($this->buildErrorResponse('更新个人信息成功'),200);
        }

        return new JsonResponse($this->buildErrorResponse('400|更新个人信息失败'), 400);
    }

    /**
     * @SWG\Post(
     *     path="/orderaddress",
     *     tags={"Orderaddress"},
     *     summary="删除地址",
     *     @SWG\Parameter(name="id", in="formData", required=false, type="string"),
     *     @SWG\Response(response="200", description="用户数据",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(type="string", property="id"),
     *              @SWG\Property(type="string", property="token"),
     *          )
     *     )
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request)
    {
        $id = $request->id;
        $orderaddress = $this->orderaddress->find($id);

        if ($orderaddress == null) {
             return new JsonResponse($this->buildErrorResponse('400|没有此地址'), 400);
        }
        $isDeleted = $orderaddress->delete();
        return new JsonResponse($isDeleted);
    }
}
