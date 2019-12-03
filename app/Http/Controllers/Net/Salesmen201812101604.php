<?php

namespace App\Http\Controllers\Net;

use App\Salesman;
use App\Shop;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Support\Facades\Validator;

class SalesmenController extends NetController
{
    private $salesman;

    public function __construct(Salesman $salesman)
    {
        $this->salesman = $salesman;
    }

    public function index()
    {
        return $this->salesman->all();
    }

    /**
     * 通过销售员ID查询服务员接口
     */
    public function users(Request $request)
    {
        $this->validateSales($request);

        $salesman_ids = explode(',', $request->input('salesman_id'));
        $user_name = $request->input('user_name');
        $shop_name = $request->input('shop_name');
        $telephone = $request->input('telephone');
        $status = $request->input('status');
        $start_date = $request->input('start_date', '1990-01-01 00:00:00');
        $end_date = $request->input('end_date', '2999-01-01 00:00:00');
        $user_at = [$start_date, $end_date];

        $users = [];
        $shopsQuery = Shop::with(['users' => function ($shopsQuery) use ($user_name, $telephone, $status, $user_at) {
                $shopsQuery->userName($user_name)
                    ->phone($telephone)
                    ->status($status)
                    ->whereBetween('created_at', $user_at)
                    ->orderBy('id', 'desc');
            }])
            ->salesman($salesman_ids)
            ->keyword($shop_name)
            ->get(['id', 'name', 'salesman_id']);

        if ($shopsQuery) {
            foreach ($shopsQuery as $key=>$shop) {
                if ($shop->users->count() > 0) {
                    foreach ($shop->users as $kk=>$user) {
                        $users[$user->id]['id'] = $user->id;
                        $users[$user->id]['salesman_id'] = $shopsQuery[$key]['salesman_id'];
                        $users[$user->id]['name'] = $user->name;
                        $users[$user->id]['gender'] = $user->gender;
                        $users[$user->id]['telephone'] = $user->telephone;
                        $users[$user->id]['status'] = $user->status;
                        $users[$user->id]['created_at'] = $user->created_at->toDateTimeString();
                        $users[$user->id]['shop_name'] = $shopsQuery[$key]['name'];
                    }
                }
            }
        }

        $data['total'] = count($users);
        $data['data'] = page_array($request->input('pageSize', 20), $request->input('page', 1), $users, 0);
        return $this->jsonReturn(200, '查询成功！', $data);
    }

    private function validateSales($request)
    {
        Validator::extendImplicit('exists_id', function($attribute, $value, $parameters, $validator) {
            if ($value) {
                $salesman_ids = explode(',', $value);
                foreach ($salesman_ids as $salesman_id) {
                    $salesman = Salesman::find($salesman_id);
                    if (!$salesman) {
                        return false;
                    }
                }
            }

            return true;
        });

        $this->validate($request, [
            'salesman_id'   => 'exists_id',
            'user_name'   => 'min:1,max:30',
            'start_date'    => 'date_format:"Y-m-d H:i:s"',
            'end_date'      => 'date_format:"Y-m-d H:i:s"|after:start_date',
        ], [
            'salesman_id.exists_id' => '数据中有营销员ID不存在',
            'user_name.*' => '服务员名称长度为1-30个字符',
            'start_date.date_format'=> '开始时间必须为日期类型',
            'end_date.date_format'  => '结束时间必须为日期类型',
            'end_date.after'        => '结束时间必须在开始日期之后',
        ]);
    }

    public function store(Request $request)
    {
        $this->validateForm($request);

        $salesman = $this->salesman->create([
            'name' => $request->name,
            'phone' => $request->phone,
            'status' => $request->status,
        ]);

        if ($salesman->id != 0) {
            return $this->jsonReturn(200, '销售员新增成功！', $salesman);
        }

        return $this->jsonReturn(400, '销售员新增失败！');
    }

    public function update(Request $request)
    {
        $this->validateForm($request, 'update');

        $salesman = $this->salesman->find($request->id);

        $isUpdated = $salesman->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'status' => $request->status,
        ]);
        if ($isUpdated) {
            return $this->jsonReturn(200, '销售员修改成功！');
        }

        return $this->jsonReturn(200, '销售员修改失败！');
    }

    protected function validateForm(Request $request, $type = null)
    {
        if ($type == 'update') {
            $this->validate($request, [
                'id'  => 'required|integer|exists:salesmen',
                'name'  => 'required|max:15|min:1',
                'phone' => 'required|regex:' . config('custom.telephone_regex') . '|unique:salesmen,phone,' . $request->id,
                'status' => 'required|in:0,1'
            ], [
                'id.required'    => 'ID不能为空',
                'id.integer'     => 'ID必须为正整数',
                'id.exists'      => 'ID不存在',
                'name.*'         => '名称为必填项,请填入1-15位中英文字符',
                'phone.required' => '手机号码不能为空',
                'phone.regex'    => '手机号码格式不正确',
                'phone.unique' => '手机号已存在，请更换手机号',
                'status.required' => '状态值不能为空',
                'status.in' => '状态值不正确'
            ]);
        } else {
            $this->validate($request, [
                'name'  => 'required|max:15|min:1',
                'phone' => 'required|regex:' . config('custom.telephone_regex') . '|unique:salesmen,phone',
                'status' => 'required|in:0,1'
            ], [
                'name.*'         => '名称为必填项,请填入1-15位中英文字符',
                'phone.required' => '手机号码为必填项',
                'phone.regex'    => '手机号码格式不正确',
                'phone.unique' => '手机号已存在，请更换手机号',
                'status.required' => '状态值不能为空',
                'status.in' => '状态值不正确'
            ]);
        }
    }
}
