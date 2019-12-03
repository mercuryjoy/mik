<?php

namespace App\Http\Controllers\API;

use App\Pay;

/**
 * @SWG\Tag(name="Pays", description="支付方式")
 */
class PaysController extends APIController
{
    private $pay;

    public function __construct(Pay $pay)
    {
        $this->pay = $pay;
    }

    /**
     * @SWG\Get(
     *     path="/pays",
     *     tags={"Pays"},
     *     summary="获取支付方式",
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function index()
    {
        $fields = ['id', 'pay_way', 'is_default'];
        $pays = $this->pay->select($fields)
            ->where('status', 1)
            ->orderBy('created_at', 'desc')
            ->get();
        return $pays;
    }
}
