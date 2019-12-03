<?php

namespace App\Http\Controllers\Net;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UsersController extends NetController
{
    /**
     * 修改当前服务员信息
     * @param Request $request
     */
    public function update(Request $request)
    {
        $this->validateUsers($request);

        $type = $request->input('type');
        $user_id = $request->input('user_id');
        $value = $request->input('value');
        $user_ids = explode(',', $user_id);

        $updateParam = [];
        foreach($user_ids as $user_id) {
            $user = User::find($user_id);

            if (!$user) {
                return $this->jsonReturn(400, '用户ID为'. $user_id . '的用户不存在');
            }

            if ($type == 'status') {
                if ($user->status == 'normal') {
                    return $this->jsonReturn(400, '用户ID为'. $user_id . '的用户状态已经为已审核');
                }
            } elseif ($type == 'active') {

                if (!in_array($value, [0, 1])) {
                    return $this->jsonReturn(400, '用户ID为'. $user_id . '的状态值不在给定范围内');
                }

                if ($user->status == 'pending') {
                    return $this->jsonReturn(400, '用户ID为'. $user_id . '的用户未审核通过，不能修改状态');
                }

                if ($value == 0 && $user->active == 0) {
                    return $this->jsonReturn(400, '用户ID为'. $user_id . '的用户状态已经为禁用');
                } elseif ($value == 1 && $user->active == 1) {
                    return $this->jsonReturn(400, '用户ID为'. $user_id . '的用户状态已经为启用');
                }
            }
        }

        $updated = false;
        if (in_array($type, ['status', 'active'])) {
            if ($type == 'status') {
                $updateParam['active'] = 1;
                $updateParam['status'] = 'normal';
            } elseif ($type == 'active') {
                $updateParam['active'] = $value;
            }

            $updated = User::whereIn('id', $user_ids)->update($updateParam);
        } elseif ($type == 'deleted') {
            $updated = User::whereIn('id', $user_ids)->delete();
        }

        if (!$updated) {
            return $this->jsonReturn(400, '用户信息更新失败');
        }

        return $this->jsonReturn(200, '用户信息更新成功');
    }

    /**
     * 修改服务员信息（姓名，性别，手机号）
     */
    public function updateProfile(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer|exists:users',
            'name' => 'required|max:30|min:2',
            'gender' => 'required|in:male,female',
            'telephone' => 'required|regex:' . config('custom.telephone_regex') . '|unique:users,telephone,'.$request->input('id'),
        ], [
            'id.required' => '服务员ID必传',
            'id.integer' => '服务员ID必须为整数',
            'id.exists' => '此服务员不存在',
            'name.*' => '姓名为必填项,请填入2-30位中英文字符',
            'gender.required' => '性别为必填项',
            'gender.in' => '性别选项不正确',
            'telephone.required' => '手机号码为必填项',
            'telephone.regex' => '手机号码格式不正确',
            'telephone.unique' => '手机号码已存在'
        ]);

        $id = $request->input('id');
        $user = User::find($id);

        $updated = $user->update($request->except('id'));

        if (!$updated) {
            return $this->jsonReturn(400, '用户信息更新失败');
        }

        return $this->jsonReturn(200, '用户信息更新成功');
    }

    /**
     * 服务员详情
     * @param $request
     */
    public function show(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer|exists:users'
        ], [
            'id.required' => '服务员ID必传',
            'id.integer' => '服务员ID必须为整数',
            'id.exists' => '此服务员不存在',
        ]);

        $id = $request->input('id');
        $user = User::with('shop')->find($id);

        if (!$user) {
            return $this->jsonReturn(400, '此用户不存在');
        }

        $data = [
            'id' => $user->id,
            'salesman_id' => $user->shop && $user->shop->salesman_id ? $user->shop->salesman_id : '',
            'name' => $user->name,
            'gender' => $user->gender,
            'telephone' => $user->telephone,
            'status' => $user->status,
            'active' => $user->active,
            'created_at' => $user->created_at->toDateTimeString(),
            'shop_name' => $user->shop && $user->shop->name ? $user->shop->name : ''
        ];
        return $this->jsonReturn(200, '用户信息查询成功', $data);
    }

    private function validateUsers($request)
    {
        Validator::extendImplicit('exists_user_id', function($attribute, $value, $parameters, $validator) {
            if ($value) {
                $user_ids = explode(',', $value);
                foreach ($user_ids as $user_id) {
                    $user = User::find($user_id);
                    if (!$user) {
                        return false;
                    }
                }
                return true;
            }
        });

        $this->validate($request, [
            'type'   => 'required|in:status,active,deleted',
            'user_id'   => 'required|exists_user_id',
            'value'   => 'required_if:type,active',
        ], [
            'type.required' => 'type 必传',
            'type.in' => 'type 值不在给定范围内',
            'user_id.required' => '服务员ID必传',
            'user_id.exists_user_id' => '数据中有服务员ID不存在',
            'value.required_if' => 'value 值为必传'
        ]);
    }
}
