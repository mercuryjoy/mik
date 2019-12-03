<?php

namespace App\Http\Controllers\Admin;

use App\Pay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PaysController extends Controller
{
    protected $pay;

    public function __construct(Pay $pay)
    {
        $this->pay = $pay;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->flash();

        $filter_status = $request->input('status', '');

        $pays = $this->pay
            ->status($filter_status)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('admin.pays.index', [
            'pays' => $pays,
            'has_filter' => strlen($filter_status) > 0,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.pays.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateForm($request);

        $fields = $request->except(['_token']);
        $pay = $this->pay->create($fields);

        if ($pay->id != 0) {
            return Redirect::route('admin.pays.index')
                ->with('message', '支付方式创建成功!')
                ->with('message-type', 'success');
        }

        return Redirect::route('admin.pays.create')
            ->withInput()
            ->withErrors($pay->errors());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pay = $this->pay->findOrFail($id);
        return view('admin.pays.edit', ['pay' => $pay]);
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
        $pay = $this->pay->find($id);
        if ($pay == null) {
            return Redirect::route('admin.pays.index')
                ->with('message', '支付方式不存在!')
                ->with('message-type', 'error');
        }

        $this->validateForm($request, $id);

        $fields = $request->except(['_token', '_method']);
        $isUpdated = $pay->update($fields);

        if ($isUpdated) {
            return Redirect::route('admin.pays.index')
                ->with('message', '支付方式修改成功!')
                ->with('message-type', 'success');
        }
        return Redirect::route('admin.pays.edit', $id)
            ->withInput()
            ->withErrors($pay->errors());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function change(Request $request, $id)
    {
        $pay = $this->pay->find($id);
        if ($pay == null) {
            return Redirect::back()
                ->with('message', '支付方式不存在!')
                ->with('message-type', 'error');
        }

        $updateParam = [];
        $type = $request->type;

        if ($type == 'status') {
            $new_status = $request->input('status');
            if (!in_array($new_status, [0, 1])) {
                return Redirect::back()
                    ->with('message', '目标状态不存在!')
                    ->with('message-type', 'error');
            }

            $updateParam = [
                'status' => $new_status,
            ];
        } elseif ($type == 'default') {
            $new_default = $request->input('is_default');
            if (!in_array($new_default, [0, 1])) {
                return Redirect::back()
                    ->with('message', '目标状态不存在!')
                    ->with('message-type', 'error');
            }

            if ($new_default == 1) {
                DB::table('pays')->update(['is_default' => 0]);
            }

            $updateParam = [
                'is_default' => $new_default,
            ];
        }

        $isUpdated = $pay->update($updateParam);
        if ($isUpdated) {
            return Redirect::back()
                ->with('message', '修改成功!')
                ->with('message-type', 'success');
        }

        return Redirect::back()
            ->with('message', '修改失败!')
            ->with('message-type', 'error');
    }


    /**
     * Validate form for store and update
     *
     * @param Request $request
     */
    protected function validateForm(Request $request, $pay = null) {

        if ($pay != null) {
            $this->validate($request, [
                'pay_way' => 'required|in:balance,alipay,wechat,line',
            ], [
                'pay_way.required' => '支付方式为必选项',
                'pay_way.in' => '支付方式不在特定选项中',
                'pay_way.unique' => '支付方式已存在，不能重复添加',
            ]);
        } else {
            $this->validate($request, [
                'pay_way' => 'required|in:balance,alipay,wechat,line|unique:pays,pay_way',
            ], [
                'pay_way.required' => '支付方式为必选项',
                'pay_way.in' => '支付方式不在特定选项中',
                'pay_way.unique' => '支付方式已存在，不能重复添加',
            ]);
        }
    }
}
