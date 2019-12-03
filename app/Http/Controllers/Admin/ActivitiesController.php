<?php

namespace App\Http\Controllers\Admin;

use App\Activity;
use Auth;
use App\Shop;
use App\User;
use App\ShopActivity;
use App\UserMoneyLog;
use App\UserPointLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;

class ActivitiesController extends Controller
{
    private $activity;

    public function __construct(Activity $activity, User $user)
    {
        $this->activity = $activity;
        $this->user = $user;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $activities = $this->activity->get();
        return view('admin.activity.index', ['activities' => $activities]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $summary_end_date = Carbon::today();
        $start_at = $summary_end_date->format("Y-m-d H:i:s");
        $end_at = $summary_end_date->addDay(2)->endOfDay()->format("Y-m-d H:i:s");
        $summary_date_range = implode(' - ', [$start_at, $end_at]);
        $request->merge(['daterange' => $summary_date_range]);
        $request->merge(['start_at' => $start_at, 'end_at' => $end_at]);

        $type = $request->input('type');
        if (!in_array($type, ['red_envelope', 'point', 'shop_owner', 'send_red_envelope'])) {
            return Redirect ::back()
                ->with('message','无法识别活动类型!')
                ->with('message-type', 'error');
        }

        $shops = [];
        $users = [];
        if (in_array($type, ['red_envelope', 'point', 'shop_owner'])) {
            $shops = $this->getShops($type);
        } elseif ($type == 'send_red_envelope') {
            $users = $this->user->with(['shop' => function ($query) {
                return $query->withTrashed();
            }])->get(['id', 'name', 'shop_id']);
        }

        // dd($users->toArray());
        return view('admin.activity.create', ['type' => $type, 'shops' => $shops, 'users' => $users]);
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
        $type = $request->input('type');

        if ($type != 'send_red_envelope') {
            $request->merge([
                'action_zone' => 'part',
                'status' => 'normal',
            ]);

            if ($type == 'point') {
                $request->merge([
                    'rule_json' => '[{"point":' . $request->input('point') . '}]',
                ]);
            } elseif ($type == 'shop_owner') {
                $request->merge([
                    'rule_json' => '[{"money":' . $request->input('money') . '}]',
                ]);

            }
            $summary_date_range = $request->input('daterange');
            $summary_dates = explode(' - ', $summary_date_range);
            $start_at = Carbon::parse($summary_dates[0]);
            $end_at = Carbon::parse($summary_dates[1]);

            $request->merge([
                'start_at' => $start_at,
                'end_at' => $end_at,
            ]);

            DB::beginTransaction();
            try {
                $rule = $this->activity->create($request->except(['_token']));

                if (!$rule->id) {
                    throw new Exception();
                }

                $shop_id_arr = explode(',', $request->input('shop_ids'));
                foreach ($shop_id_arr as $item) {
                    $shop_activity = ShopActivity::create([
                        'shop_id' => $item,
                        'activity_id' => $rule->id,
                    ]);

                    if (!$shop_activity) {
                        throw new Exception();
                    }
                }

                DB::commit();
                return Redirect::route('admin.activities.index')
                    ->with('message', '活动创建成功!')
                    ->with('message-type', 'success');
            } catch (\Exception $e) {
                DB::rollBack();
                return Redirect::route('admin.activities.create', ['type' => $type])
                    ->with('message', '活动创建失败!')
                    ->with('message-type', 'error');
            }
        } else {
            DB::beginTransaction();
            try {
                $user_id_arr = explode(',', $request->input('user_ids'));
                foreach ($user_id_arr as $user_id) {

                    $money_amount = intval(doubleval($request->input('money_amount')) * 100);
                    $point_amount = intval($request->input('point_amount'));

                    if ($money_amount > 0) {
                        $user_money_log = UserMoneyLog::create([
                            'type' => 'red_envelope',
                            'amount' => $money_amount,
                            'user_id' => $user_id,
                            'admin_id' => Auth::User()->id,
                            'comment' => '发红包',
                        ]);

                        if (! $user_money_log) {
                            throw new Exception();
                        }
                    }

                    if ($point_amount > 0) {
                        $user_point_log = UserPointLog::create([
                            'type' => 'red_envelope',
                            'amount' => $point_amount,
                            'user_id' => $user_id,
                            'admin_id' => Auth::User()->id,
                            'comment' => '发红包',
                        ]);

                        if (! $user_point_log) {
                            throw new Exception();
                        }
                    }

                    // update wallet
                    $user = $this->user->find($user_id);
                    $user->money_balance += $money_amount;
                    $user->point_balance += $point_amount;
                    $userObj = $user->save();

                    if (! $userObj) {
                        throw new Exception();
                    }
                }

                DB::commit();
                return Redirect::route('admin.activities.index')
                    ->with('message', '红包发送成功!')
                    ->with('message-type', 'success');
            } catch (\Exception $e) {
                DB::rollBack();
                return Redirect::route('admin.activities.create', ['type' => $type])
                    ->with('message', '红包发送失败!')
                    ->with('message-type', 'error');
            }
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $fields = ['activities.id', 'activities.title', 'activities.start_at', 'activities.end_at', 'activities.action_zone', 'activities.type', 'activities.status', 'activities.created_at', 'activities.rule_json',
            DB::raw('group_concat(s.name) as shop_names'),
            DB::raw('group_concat(sa.shop_id) as shop_ids'),
        ];

        $activity = $this->activity->select($fields)
            ->leftJoin('shop_activity as sa', 'sa.activity_id', '=', 'activities.id')
            ->leftJoin('shops as s', 'sa.shop_id', '=', 's.id')
            ->where('activities.id', $id)
            ->first();

        if ($activity) {
            if (in_array($activity->type, ['point', 'shop_owner'])) {
                $param = json_decode($activity->rule_json, true);
                $key = array_keys($param[0])[0];
                $value = $param[0][$key];
                $activity->$key = $value;
            }

            $activity->daterange = $activity->start_at . ' - ' . $activity->end_at;
        }

        $shops = [];
        if ($activity->action_zone == 'part') {
            $shops = $this->getShops($activity->type, $activity->id);
        }

        return view('admin.activity.edit', ['activity' => $activity, 'shops' => $shops]);
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
        $activity = $this->activity->find($id);
        if ($activity == null) {
            return Redirect::route('admin.activities.index')
                ->with('message', '活动不存在!')
                ->with('message-type', 'error');
        }

        $this->validateForm($request, $activity->action_zone);

        $type = $request->input('type');
        $action_zone = $request->input('action_zone');
        $shop_id_arr = explode(',', $request->input('shop_ids'));
        if ($type == 'point') {
            $request->merge([
                'rule_json' => '[{"point":' . $request->input('point') . '}]',
            ]);
        } elseif ($type == 'shop_owner') {
            $request->merge([
                'rule_json' => '[{"money":' . $request->input('money') . '}]',
            ]);

        }
        $summary_date_range = $request->input('daterange');
        
        if (!empty($summary_date_range)) {
            $summary_dates = explode(' - ', $summary_date_range);
            $start_at = Carbon::parse($summary_dates[0]);
            $end_at = Carbon::parse($summary_dates[1]);
            $request->merge([
            'start_at' => $start_at,
            'end_at' => $end_at,
            ]);
        }

        DB::beginTransaction();
        try {
            $isUpdated = $activity->update($request->except(['_token']));

            if (!$isUpdated) {
                throw new Exception();
            }

            if ($action_zone == 'part') {
                $edit_self_shops = ShopActivity::where('activity_id', $id)->pluck('shop_id');
                $edit_self_shop_ids = $edit_self_shops->toArray();

                $delete_shop_ids = array_diff($edit_self_shop_ids, $shop_id_arr);
                $insert_shop_ids = array_diff($shop_id_arr, $edit_self_shop_ids);

                if (count($delete_shop_ids) > 0) {
                    foreach ($delete_shop_ids as $item) {
                        $delete_res = ShopActivity::where('activity_id', $id)
                            ->where('shop_id', $item)
                            ->delete();

                        if (!$delete_res) {
                            throw new Exception();
                        }
                    }
                }

                if (count($insert_shop_ids) > 0) {
                    foreach ($insert_shop_ids as $item) {
                        $shop_activity = ShopActivity::create([
                            'shop_id' => $item,
                            'activity_id' => $id,
                        ]);

                        if (!$shop_activity) {
                            throw new Exception();
                        }
                    }
                }
            }

            DB::commit();
            return Redirect::route('admin.activities.index')
                ->with('message', '活动修改成功!')
                ->with('message-type', 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::route('admin.activities.update', $id)
                ->withInput()
                ->withErrors($activity->errors());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function change(Request $request, Activity $activity)
    {
        $status = $request->input('status');
        if (! in_array($status, ['stop', 'normal'])) {
            return Redirect::route('admin.activities.index')
                ->with('message', '状态值有误!')
                ->with('message-type', 'error');
        }

        if (!$activity) {
            return Redirect::route('admin.activities.index')
                ->with('message', '活动不存在!')
                ->with('message-type', 'error');
        }

        if ($activity->status == $status) {
            return Redirect::route('admin.activities.index')
                ->with('message', '状态不能和该活动状态相同!')
                ->with('message-type', 'error');
        }

        $updated = $activity->update($request->except(['_method', '_token']));

        if (!$updated) {
            return Redirect::route('admin.activities.index')
                ->with('message', '活动状态修改失败!')
                ->with('message-type', 'error');
        }

        return Redirect::route('admin.activities.index')
            ->with('message', '修改活动状态成功!')
            ->with('message-type', 'success');
    }

    /**
     * 筛选出适合条件的终端数据
     * @param Request $request
     * @param $action_zone
     */
    private function getShops($type, $activity_id = null)
    {
        $shopActivities = ShopActivity::with(['shop', 'activity' => function ($shops) use ($type) {
                $shops->where('type', $type)
                    ->where('status', 'normal');
            }])
            ->orderBy('id', 'desc')
            ->get();

        $filter_not_show_shop_ids = [];
        foreach ($shopActivities as $item) {
            if ($item->activity) {
                if ($item->activity->type && $item->activity->type == $type) {
                    $filter_not_show_shop_ids[] = $item->shop ? $item->shop->id : '';
                }
            }
        };

        if ($activity_id) {
            $edit_self_shops = ShopActivity::where('activity_id', $activity_id)->pluck('shop_id');
            $edit_self_shop_ids = $edit_self_shops->toArray();
            $filter_not_show_shop_ids = array_diff($edit_self_shop_ids, $filter_not_show_shop_ids);
        }

        $shops = Shop::with('area')->get(['id', 'name', 'address', 'area_id'])->toArray();

        if (count($filter_not_show_shop_ids) > 0) {
            foreach ($shops as $key=>$item) {
                foreach ($filter_not_show_shop_ids as $value) {
                    if ($item['id'] == $value) {
                        unset($shops[$key]);
                    }
                }
            }
        }

        return $shops;
    }

    private function validateForm(Request $request, $action_zone) {
        Validator::extendImplicit('rule_json_format', function($attribute, $value, $parameters, $validator) {
            if ($parameters[0] == 'red_envelope') {
                $data = json_decode($value);
                if (!is_array($data)) return false;
                foreach ($data as $rule ) {
                    if (!is_object($rule)) return false;
                    if (!isset($rule->percentage)) return false;
                    if (!isset($rule->min)) return false;
                    if (!isset($rule->max)) return false;
                    if ($rule->max <= 0 || $rule->min > $rule->max) return false;
                }
            }
            return true;
        });

        Validator::extendImplicit('rule_json_sum', function($attribute, $value, $parameters, $validator) {
            if ($parameters[0] == 'red_envelope') {
                $data = json_decode($value);
                if (!is_array($data)) return false;
                $sum = 0;
                foreach ($data as $rule) {
                    if (!is_object($rule)) return false;
                    if (!isset($rule->percentage)) return false;
                    $sum += $rule->percentage;
                }
                return $sum == 100;
            }
            return true;
        });

        Validator::extendImplicit('shop_exists', function($attribute, $value, $parameters, $validator) {
            $shopIdArr = explode(',', $value);
            foreach ($shopIdArr as $shop ) {
                $shopObj = Shop::find($shop);
                if (!$shopObj) {
                    return false;
                }
            }
            return true;
        });

        Validator::extendImplicit('user_exists', function($attribute, $value, $parameters, $validator) {
            $userIdArr = explode(',', $value);
            foreach ($userIdArr as $user ) {
                $userObj = User::find($user);
                if (!$userObj) {
                    return false;
                }
            }
            return true;
        });

        Validator::extendImplicit('shop_pass', function($attribute, $value, $parameters, $validator) {
            $type = $parameters[0];
            $request_type = $parameters[1];
            $shopIdArr = explode(',', $value);
            if ($request_type == 'POST') {
                $shops = ShopActivity::with(['activity'])
                    ->whereIn('shop_id', $shopIdArr)
                    ->get();

                foreach ($shops as $shop) {
                    if ($shop->activity) {
                        if ($type == $shop->activity->type && $shop->activity->status == 'start') {
                            return false;
                        }
                    }
                }
            }

            return true;
        });

        $type = $request->input('type');
        $method = $request->method();

        if ($type != 'send_red_envelope') {
            if ($action_zone != null && $action_zone == 'all') {
                $this->validate($request, [
                    'title' => 'required|min:1|max:30',
                    'type' => 'required|in:red_envelope,point,shop_owner',
                    'point' => 'required_if:type,point|min:0|max:1000',
                    'money' => 'required_if:type,shop_owner|min:0|max:500',
                    'rule_json' => 'required_if:type,red_envelope|json|rule_json_sum:'.$type.'|rule_json_format:'.$type,
                ], [
                    'title.*' => '地区为必填项，字数限制在1-30个字符之间',
                    'type.*' => '活动类型有误',
                    'point.*' => '积分数必填，积分值在0-1000之间',
                    'money.*' => '金额必填，金额在0-500之间',
                    'rule_json.required_if' => '活动数据必填',
                    'rule_json.json' => '活动数据有误',
                    'rule_json.rule_json_format' => '活动数据有误',
                    'rule_json.rule_json_sum' => '活动占比总和须为100%',
                ]);
            } else {
                $this->validate($request, [
                    'title' => 'required|min:1|max:30',
                    'type' => 'required|in:red_envelope,point,shop_owner',
                    'action_zone' => 'required|in:all,part',
                    'daterange' => 'required_if:action_zone,part',
                    'point' => 'required_if:type,point|min:0|max:1000',
                    'money' => 'required_if:type,shop_owner|min:0|max:500',
                    'shop_ids' => 'required|shop_exists|shop_pass:'.$type.','.$method,
                    'rule_json' => 'required_if:type,red_envelope|json|rule_json_sum:'.$type.'|rule_json_format:'.$type,
                ], [
                    'title.*' => '地区为必填项，字数限制在1-30个字符之间',
                    'type.*' => '活动类型有误',
                    'daterange.required_if' => '开始时间必填',
                    'point.*' => '积分数必填，积分值在0-1000之间',
                    'money.*' => '金额必填，金额在0-500之间',
                    'shop_ids.required' => '适用终端必选',
                    'shop_ids.shop_exists' => '适用终端中有不存在或者删除的终端',
                    'shop_ids.shop_pass' => '适用终端中有终端已选择了相同类型的活动，不能重复选择',
                    'rule_json.required_if' => '活动数据必填',
                    'rule_json.json' => '活动数据格式错误',
                    'rule_json.rule_json_format' => '活动数据格式错误',
                    'rule_json.rule_json_sum' => '活动占比总和须为100%',
                ]);
            }
        } else {
            $this->validate($request, [
                'money_amount' => 'required_without:point_amount|numeric|min:0.01|max:500',
                'point_amount' => 'required_without:money_amount|numeric|min:1|max:1000',
                'user_ids' => 'required|user_exists',
            ], [
                'money_amount.required_without' => '现金金额和积分数量至少要填写一个',
                'point_amount.required_without' => '现金金额和积分数量至少要填写一个',
                'money_amount.*' => '红包金额续在0.01~500之间',
                'point_amount.*' => '积分数量续在1~1000之间',
                'user_ids.required' => '服务员必选',
                'user_ids.user_exists' => '所选服务员中有不存在或者被删除的服务员',
            ]);
        }
    }
}
