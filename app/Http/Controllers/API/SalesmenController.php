<?php

namespace App\Http\Controllers\API;

use App\Salesman;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use DB;
/**
 * @SWG\Tag(name="Salesman", description="终端")
 */
class SalesmenController extends APIController
{
    private $salesman;

    public function __construct(Salesman $salesman)
    {
        $this->salesman = $salesman;
    }
    /**
     * @SWG\Get(
     *     path="/salesman",
     *     tags={"Salesman"},
     *     summary="获取销售员详情",
     *     @SWG\Parameter(name="q", in="query", required=false, type="string"),
     *     @SWG\Response(response="200", description="搜索销售员")
     *     )
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request) 
    {
        $id = $request->salesman_id;
        $salesman = $this->salesman->find($id);
        if (!$salesman) {
             return new JsonResponse($this->buildErrorResponse('查询失败,无此销售员'),404);
        }
        $shops = $salesman->toArray();
        return new JsonResponse($shops);
    }
    public function shopmen(Request $request) 
    {
        $this->validate($request, [
            'shop_id' => 'required|regex:/(^[\-0-9][0-9]*(.[0-9]+)?)$/'
        ], [
            'shop_id.required' => '101|shop_id为数字不能为空',
            'shop_id.regex' => '102|shop_id为数字',
        ]);
        $id = $request->shop_id;
        // dd($id);
        $shops= DB::table('shops')
            ->where('id','=',$id)
            ->get();
        $salesman_id=$shops['0']->salesman_id;
        if (!$shops) {
        
        return new JsonResponse($this->buildErrorResponse('查询失败,无此销售员'),404);
        }
        $salesman = $this->salesman->find($salesman_id);
        if (!$salesman) {
             return new JsonResponse($this->buildErrorResponse('查询失败,无此销售员'),404);
        }
        $shops = $salesman->toArray();
        return new JsonResponse($shops);
    }

}


