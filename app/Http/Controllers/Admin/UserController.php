<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Contracts\SMSContract;
use App\ScanLog;
use App\Shop;
use App\UserMoneyLog;
use App\UserPointLog;
use App\UserShopChangeLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $date_range = $request->input('daterange');
        $dates = explode(' - ', $date_range);
        try {
            $start_date = Carbon::parse($dates[0]);
            $end_date = Carbon::parse($dates[1]);
        } catch(\Exception $e) {
            $end_date = Carbon::today();
            $start_date = $end_date->copy()->subMonth(1)->addDay(1);
            $date_range = implode(' - ', [$start_date->format("Y-m-d"), $end_date->format("Y-m-d")]);
            $request->merge(['daterange' => $date_range]);
        }

        $filter_date_range = $request->input('filter_daterange');
        $filter_dates = explode(' - ', $filter_date_range);
        try {
            $filter_start_date = Carbon::parse($filter_dates[0]);
            $filter_end_date = Carbon::parse($filter_dates[1]);
        } catch (\Exception $e) {
            $filter_start_date = Carbon::minValue();
            $filter_end_date = Carbon::maxValue();
        }

        $request->flash();

        $filter_shop_keyword = $request->input('filter_shop_keyword');
        $filter_phone_keyword = $request->input('filter_phone_keyword');
        $filter_salesman_keyword = $request->input('filter_salesman_keyword');
        $filter_user_keyword = $request->input('filter_user_keyword');
        $filter_area_id = $request->input('area_id');
        $filter_status = $request->input('status');
        $filter_delete_status = $request->input('filter_delete_status');

        $usersQuery = $this->user
            ->withTrashed()
            ->filterDeleteStatus($filter_delete_status)
            ->status($filter_status)
            ->shopName($filter_shop_keyword)
            ->phone($filter_phone_keyword)
            ->area($filter_area_id)
            ->salesmanName($filter_salesman_keyword)
            ->userName($filter_user_keyword)
            ->whereBetween('created_at', [$filter_start_date, $filter_end_date->endOfDay()])
            ->with(['shop', 'shop.area', 'scanLog' => function($query) use ($start_date, $end_date) {
                $query->where('type', 'scan_prize')->whereBetween('created_at', [$start_date, $end_date->endOfDay()]);
            }, 'userScanGetPointLog' => function($query) use ($start_date, $end_date) {
                $query->where('type', 'scan_send_money_activity')
                     ->where('point', '>', 0)
                     ->whereBetween('created_at', [$start_date, $end_date->endOfDay()]);
            }])
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');

        if ($request->input('export') === 'xls') {
            $users = $usersQuery->get();
            Excel::create('服务员列表', function($excel) use($users) {
                $excel->sheet('Sheet', function($sheet) use($users) {
                    $sheet->loadView('admin.user.index_xls')
                        ->with('users', $users);
                });
            })->export('xlsx');
        }

        $users = $usersQuery->paginate(50);

        // print_r($users->toArray());die;

        return view('admin.user.index',
            [
                'users' => $users,
                'has_filter' => strlen($filter_shop_keyword) > 0 || strlen($filter_phone_keyword) > 0 || strlen($filter_area_id) > 0 || strlen($filter_status) > 0 || strlen($filter_date_range) > 0 || strlen($filter_delete_status) > 0,
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateForm($request, null);

        $user = $this->user->create($request->except(['_token']));

        if ($user->id != 0) {
            return Redirect::route('admin.users.index')
                ->with('message', '服务员创建成功!')
                ->with('message-type', 'success');
        }
        return Redirect::route('admin.users.create')
            ->withInput()
            ->withErrors($user->errors());
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $show = $request->input('show');
        $date_range = $request->input('daterange');
        $dates = explode(' - ', $date_range);
        try {
            $start_date = Carbon::parse($dates[0]);
            $end_date = Carbon::parse($dates[1]);
        } catch(\Exception $e) {
            $end_date = Carbon::today();
            $start_date = $end_date->copy()->subMonth(1)->addDay(1);
            $date_range = implode(' - ', [$start_date->format("Y-m-d"), $end_date->format("Y-m-d")]);
            $request->merge(['daterange' => $date_range]);
        }

        $request->flash();

        $user = $this->user->withTrashed()->find($id);
        $scanLogs = ScanLog::where('user_id', $id)
            ->whereBetween('created_at', [$start_date, $end_date->endOfDay()])
            ->orderBy('created_at', 'desc')->get();
        $shopChangeLogs = UserShopChangeLog::where('user_id', $id)
            ->with('before_shop')
            ->orderBy('created_at', 'asc')
            ->get();

        $serviceShopLogs = [];
        $startDate = $user->created_at;
        foreach ($shopChangeLogs as $changeLog) {
            $serviceShopLogs[] = [
                'shop' => $changeLog->before_shop,
                'start_time' => $startDate,
                'end_time' => $changeLog->created_at,
            ];
            $startDate = $changeLog->created_at;
        }
        if ($user->shop != null) {
            $serviceShopLogs[] = [
                'shop' => $user->shop,
                'start_time' => $startDate,
                'end_time' => null,
            ];
        }

        $moneyLogs = UserMoneyLog::where('user_id', $id)
            ->whereBetween('created_at', [$start_date, $end_date->endOfDay()])
            ->orderBy('created_at', 'desc')->get();

        $pointLogs = UserPointLog::where('user_id', $id)
            ->whereBetween('created_at', [$start_date, $end_date->endOfDay()])
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.user.show',
            [
                'user' => $user,
                'scan_logs' => $scanLogs,
                'service_shop_logs' => $serviceShopLogs,
                'money_logs' => $moneyLogs,
                'point_logs' => $pointLogs,
                'show' => $show ? $show : 'money',
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = $this->user->withTrashed()->findOrFail($id);
        return view('admin.user.edit', ['user' => $user]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, SMSContract $sms)
    {
        $user = $this->user->withTrashed()->find($id);
        $originStatus = $user->status;
        $originShopId = $user->shop_id;
        $newShopId = $request->input('shop_id');

        if (! $user) {
            return Redirect::route('admin.users.index')
                ->with('message', '服务员不存在!')
                ->with('message-type', 'error');
        }

        $this->validateForm($request, $user);

        $isUpdated = $user->update($request->except(['_token']));
        if ($isUpdated) {

            if ($originShopId != $newShopId) {
                // query the user is not owner
                $shop = Shop::where('owner_id', $id)
                    ->first();
                if ($shop) {
                    $shop->owner_id = 0;
                    $shop->save();
                }
            }

            if ($originStatus == 'pending' && $user->status = 'normal') {
                $sms->sendPassAuditMessage($user->telephone);
            }

            return Redirect::route('admin.users.index')
                ->with('message', '服务员修改成功!')
                ->with('message-type', 'success');
        }
        return Redirect::route('admin.users.update', $id)
            ->withInput()
            ->withErrors($user->errors());
    }

    public function updateStatus(Request $request, $id, SMSContract $sms)
    {
        $user = $this->user->withTrashed()->find($id);
        $originStatus = $user->status;

        if ($user == null) {
            return Redirect::back()
                ->with('message', '服务员不存在!')
                ->with('message-type', 'error');
        }

        $validator = $this->getValidationFactory()->make($request->all(), [
            'status' => 'required|in:pending,normal'
        ], [
            'status.*' => '状态为必填项',
        ]);

        if ($validator->fails()) {
            return Redirect::back()
                ->with('message', '服务员审核状态修改有误!')
                ->with('message-type', 'error');
        }

        $status = $request->input('status');
        $param = ['status' => $status];
        if ($status == 'normal') {
            $param['active'] = 1;
        }

        $isUpdated = $user->update($param);
        if ($isUpdated) {

            if ($originStatus == 'pending' && $user->status = 'normal') {
                $sms->sendPassAuditMessage($user->telephone);
            }

            return Redirect::back()
                ->with('message', '服务员审核状态修改成功!')
                ->with('message-type', 'success');
        }
        return Redirect::back()
            ->with('message', '服务员审核状态修改有误!')
            ->with('message-type', 'error');
    }

    public function destroy($id)
    {
        $user = $this->user->find($id);
        if ($user == null) {
            return Redirect::back()
                ->with('message', '服务员不存在!')
                ->with('message-type', 'error');
        }

        $isDeleted = $user->delete();
        return Redirect::back()
            ->with('message', $isDeleted ? '服务员已禁用!' : '服务员禁用失败!')
            ->with('message-type', $isDeleted ? 'success' : 'error');
    }

    /**
     * Restore a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        $user = $this->user->onlyTrashed()->find($id);
        if (! $user) {
            return Redirect::back()
                ->with('message', '服务员不存在!')
                ->with('message-type', 'error');
        }

        $restore = $user->restore();

        if (! $restore) {
            return Redirect::back()
                ->with('message', '服务员状态修改失败!')
                ->with('message-type', 'error');
        }

        return Redirect::back()
            ->with('message', '修改服务员状态成功!')
            ->with('message-type', 'success');
    }

    /*
     * 功能已移动至活动页面
     public function sendRedEnvelope(Request $request, $id) {
        // validate user
        $user = $this->user->withTrashed()->find($id);
        if ($user == null) {
            return Redirect::route('admin.users.index')
                ->with('message', '服务员不存在!')
                ->with('message-type', 'error');
        }

        // validate amount
        $validator = $this->getValidationFactory()->make($request->all(), [
            'money_amount' => 'required_without:point_amount|numeric|min:0.01|max:200',
            'point_amount' => 'required_without:money_amount|numeric|min:1|max:1000',
        ], [
            'money_amount.required_without' => '现金金额和积分数量至少要填写一个',
            'point_amount.required_without' => '现金金额和积分数量至少要填写一个',
            'money_amount.*' => '红包金额续在0.01~200之间',
            'point_amount.*' => '积分数量续在1~1000之间',
        ]);

        if ($validator->fails()) {
            return Redirect::back()
                ->with('message', $validator->getMessageBag()->first())
                ->with('message-type', 'error');
        }

        $money_amount = intval(doubleval($request->input('money_amount')) * 100);
        $point_amount = intval($request->input('point_amount'));

        if ($money_amount > 0) {
            UserMoneyLog::create([
                'type' => 'red_envelope',
                'amount' => $money_amount,
                'user_id' => $user->id,
                'admin_id' => Auth::User()->id,
                'comment' => '发红包',
            ]);
        }

        if ($point_amount > 0) {
            UserPointLog::create([
                'type' => 'red_envelope',
                'amount' => $point_amount,
                'user_id' => $user->id,
                'admin_id' => Auth::User()->id,
                'comment' => '发红包',
            ]);
        }

        // update wallet
        $user->money_balance += $money_amount;
        $user->point_balance += $point_amount;
        $user->save();

        return Redirect::back()
            ->with('message', '红包已发放!')
            ->with('message-type', 'success');
    }*/

    /**
     * 微信解绑
     * @param $id
     * @return mixed
     */
    public function untie($id)
    {
        $user = $this->user->find($id);
        if ($user == null) {
            return Redirect::back()
                ->with('message', '服务员不存在!')
                ->with('message-type', 'error');
        }

        if (! $user->wechat_openid) {
            return Redirect::back()
                ->with('message', '此服务员未绑定微信!')
                ->with('message-type', 'error');
        }

        $isUntied = $user->update(['wechat_openid' => null]);
        return Redirect::back()
            ->with('message', $isUntied ? '微信解绑成功!' : '微信解绑失败!')
            ->with('message-type', $isUntied ? 'success' : 'error');
    }

    /**
     * Validate form for store and update
     *
     * @param Request $request
     */
    protected function validateForm(Request $request, $user) {
        $this->validate($request, [
            'name' => 'required|max:30|min:2',
            'gender' => 'required|in:male,female',
            'shop_id' => 'required|exists:shops,id',
            'telephone' => 'required|regex:' . config('custom.telephone_regex') . '|unique:users,telephone' . ($user ? ',' . $user->id : ''),
            'status' => 'required|in:pending,normal'
        ], [
            'name.*' => '姓名为必填项,请填入2-30位中英文字符',
            'gender.*' => '性别为必填项',
            'shop_id.*' => '终端未找到',
            'telephone.required' => '手机号码未必填项',
            'telephone.regex' => '手机号码格式不正确',
            'telephone.unique' => '手机号码已被其他服务员使用',
            'status.*' => '状态为必填项',
        ]);
    }
}
