<?php

namespace App\Http\Controllers\Admin;

use App\Salesman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class SalesmanController extends Controller
{
    protected $salesman;

    public function __construct(Salesman $salesman)
    {
        $this->salesman = $salesman;
    }

    //首页展示
    public function index(Request $request)
    {
        $filter_id = $request->input('filter_id');
        $filter_name = $request->input('filter_name');
        $filter_phone = $request->input('filter_phone');
        $filter_status = $request->input('filter_status');
        //数据库操作，降序排序。分页
        $salesmen = $this->salesman
                ->filterId($filter_id)
                ->filterName($filter_name)
                ->filterPhone($filter_phone)
                ->filterStatus($filter_status)
                ->latest()
                ->paginate(50);
        //渲染视图；
        return view('admin.salesman.index', compact('salesmen', 'filter_status'));
    }

    //新建营销员
    public function create()
    {
        return view('admin.salesman.create');
    }

    public function store(Request $request)
    {
        $this->validateForm($request);

        $salesman = $this->salesman->create($request->except(['_token']));

        if ($salesman->id != 0) {
            return Redirect::route('admin.salesmen.index')
                ->with('message', '销售员创建成功!')
                ->with('message-type', 'success');
        }
        return Redirect::route('admin.salesman.create')
            ->withInput()
            ->withErrors($salesman->errors());
    }

    //详情修改
    public function edit($id)
    {
        $salesman = $this->salesman->find($id);
        return view('admin.salesman.edit', ['salesman' => $salesman]);
    }

    public function update(Request $request, $id)
    {
        $this->validateForm($request);

        $salesman = $this->salesman->find($id);
        if ($salesman == null) {
            return Redirect::route('admin.shops.index')
                ->with('message', '销售员不存在!')
                ->with('message-type', 'error');
        }

        $isUpdated = $salesman->update($request->except(['_token']));
        if ($isUpdated) {
            return Redirect::route('admin.salesmen.index')
                ->with('message', '销售员修改成功!')
                ->with('message-type', 'success');
        }
        return Redirect::route('admin.salesman.update', $id)
            ->withInput()
            ->withErrors($salesman->errors());
    }

    public function destroy($id)
    {
        $salesman = $this->salesman->find($id);

        if ($salesman == null) {
            return Redirect::route('admin.salesmen.index')
                ->with('message', '销售员不存在!')
                ->with('message-type', 'error');
        }

        if ($salesman->shops->isEmpty() === false) {
            return Redirect::route('admin.salesmen.index')
                ->with('message', '仍有终端与该销售员绑定，无法删除!')
                ->with('message-type', 'error');
        }

        $isDeleted = $salesman->delete();
        return Redirect::back()
            ->with('message', $isDeleted ? '销售员已删除!' : '销售员删除失败!')
            ->with('message-type', $isDeleted ? 'success' : 'error');
    }

    protected function validateForm(Request $request)
    {
        $this->validate($request, [
            'name'  => 'required|max:15|min:1',
            'phone' => 'required|regex:' . config('custom.telephone_regex'),
        ], [
            'name.*'         => '名称为必填项,请填入1-15位中英文字符',
            'phone.required' => '手机号码为必填项',
            'phone.regex'    => '手机号码格式不正确',
        ]);
    }
}
