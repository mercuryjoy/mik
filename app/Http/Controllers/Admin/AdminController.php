<?php

namespace App\Http\Controllers\Admin;

use Gate;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use App\Http\Requests;
use App\Admin;

class AdminController extends Controller
{
    protected $admin;

    public function __construct(Admin $admin)
    {
        $this->admin = $admin;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (Gate::denies('list-admin')) {
            return redirect()->route('admin.admins.edit', [Auth::User()->id]);
        }

        $request->flash();

        if (Auth::User()->isSuperAdmin()) {
            $admins = Admin::whereNotIn('id', [1, 32])->paginate(20);
        } else {
            $admins = Admin::whereNotIn('id', [1, 32])->paginate(20);
        }

    	return view('admin.admin.index', ['admins' => $admins]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.admin.create', ['levels' => $this->supportedLevels()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:4|max:30',
            'email' => 'required|email|max:50|unique:admins,email',
            'password' => 'required|min:6|max:20',
            'level' => 'required|min:1|max:999',
        ], [
            'name.*' => '名称为必填项,请填入4-30位中英文字符',
            'email.unique' => '该Email已注册为管理员',
            'email.*' => 'Email为必填项,请填入正确Email',
            'password.*' => '密码为必填项,请填入6-20位中英文字符',
            'level.*' => '请选择正确级别',
        ]);

        $admin = $this->admin->create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'level' => $request->input('level'),
        ]);

        if ($admin->id != 0) {
            return Redirect::route('admin.admins.index')
                ->with('message', '管理员创建成功!')
                ->with('message-type', 'success');
        }
        return Redirect::route('admin.admins.create')
            ->withInput()
            ->withErrors($admin->errors());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $admin = $this->admin->find($id);
        return view('admin.admin.show', ['admin' => $admin]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $admin = Admin::findOrFail($id);

        if (Gate::denies('edit-admin', $admin)) {
            return abort(403);
        }

        return view('admin.admin.edit', ['admin' => $admin, 'levels' => $this->supportedLevels()]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $admin = $this->admin->find($id);
        if ($admin == null) {
            return Redirect::route('admin.admins.index')
                ->with('message', '管理员不存在!')
                ->with('message-type', 'error');
        }

        if (Gate::denies('edit-admin', $admin)) {
            return abort(403);
        }

        if (!in_array($request->input('action'), ['change_self', 'change_other', 'change_password'])) {
            return Redirect::route('admin.admins.index')
                ->with('message', '操作有误, 请联系管理人员!')
                ->with('message-type', 'error');
        }

        switch ($request->input('action')) {
            case 'change_self':
                $this->validate($request, [
                    'name' => 'required|min:4|max:30',
                    'email' => 'required|email|max:50|unique:admins,email,' . $admin->id,
                ], [
                    'name.*' => '名称为必填项,请填入4-30位中英文字符',
                    'email.unique' => '该Email已注册为管理员',
                    'email.*' => 'Email为必填项,请填入正确Email',
                ]);
                $admin->name = $request->input('name');
                $admin->email = $request->input('email');
                break;
            case 'change_other':
                $this->validate($request, [
                    'name' => 'required|min:4|max:30',
                    'email' => 'required|email|max:50|unique:admins,email,' . $admin->id,
                    'password' => 'min:6|max:20',
                    'level' => 'required|min:1|max:999',
                ], [
                    'name.*' => '名称为必填项,请填入4-30位中英文字符',
                    'email.unique' => '该Email已注册为管理员',
                    'email.*' => 'Email为必填项,请填入正确Email',
                    'password.*' => '密码长度应在6-20位中英文字符',
                    'level.*' => '请选择正确级别',
                ]);
                $admin->name = $request->input('name');
                $admin->email = $request->input('email');
                if (strlen($request->input('password')) > 0) {
                    $admin->password = bcrypt($request->input('password'));
                }
                $admin->level = $request->input('level');
                break;
            case 'change_password':
                Validator::extendImplicit('bcrypt_same', function($attribute, $value, $parameters, $validator) {
                    return Hash::check($value, $parameters[0]);
                });

                $this->validate($request, [
                    'password' => 'required|bcrypt_same:' . $admin->password,
                    'new_password' => 'required|confirmed|min:6|max:20',
                ], [
                    'password.*' => '当前密码填写有误',
                    'new_password.confirmed' => '两次密码输入不一致',
                    'new_password.*' => '密码长度应在6-20位中英文字符',
                ]);

                $admin->password = bcrypt($request->input('new_password'));
                break;
        }

        $isUpdated = $admin->save();
        if ($isUpdated) {
            return Redirect::route('admin.admins.index')
                ->with('message', '修改成功!')
                ->with('message-type', 'success');
        }
        return Redirect::route('admin.admins.update', $id)
            ->withInput()
            ->withErrors($admin->errors());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $admin = $this->admin->find($id);
        if ($admin == null) {
            return Redirect::route('admin.admins.index')
                ->with('message', '管理员不存在!')
                ->with('message-type', 'error');
        }

        if (Gate::denies('destroy-admin', $admin)) {
            return abort(403);
        }

        $isDeleted = $admin->delete();
        return Redirect::back()
            ->with('message', $isDeleted ? '管理员已删除!' : '管理员删除失败!')
            ->with('message-type', $isDeleted ? 'success' : 'error');
    }

    protected function supportedLevels()
    {
        $levels = ["1" => "管理员"];
        if (Auth::User()->isSeniorAdmin()) {
            $levels["2"] = "高级管理员";
            $levels["3"] = "销售部";
            $levels["4"] = "销售管理";
            $levels["5"] = "财务管理";
        }
        if (Auth::User()->isSuperAdmin()) {
            $levels["99"] = "超级管理员";
        }
        return $levels;
    }
}
