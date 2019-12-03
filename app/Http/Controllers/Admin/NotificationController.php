<?php

namespace App\Http\Controllers\Admin;

use Efriandika\LaravelSettings\Facades\Settings;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $phones_str = Settings::get('notification.phones', '[]');
        $phones = json_decode($phones_str);

        $daily_money_threshold = Settings::get('notification.threshold.daily_money_sum', 100000000);
        $daily_user_scan_threshold = Settings::get('notification.threshold.per_user_scan_count', 10000);
        $daily_funding_pool_threshold = Settings::get('notification.threshold.funding_pool_balance', 0);

        return view('admin.notification.index', [
            'telephones' => $phones,
            'notification_daily_money_threshold' => $daily_money_threshold,
            'notification_daily_user_scan_threshold' => $daily_user_scan_threshold,
            'notification_funding_pool_threshold' => $daily_funding_pool_threshold,
        ]);
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
            'telephone' => 'required|regex:' . config('custom.telephone_regex')
        ], [
            'telephone.*' => '请填入正确手机号'
        ]);

        $telephone = $request->input('telephone');

        $phones_str = Settings::get('notification.phones', '[]');
        $phones = json_decode($phones_str, true);
        if (!in_array($telephone, $phones)) {
            $phones[] = $telephone;
            Settings::set('notification.phones', json_encode($phones));
            return Redirect::route('admin.notifications.index')
                ->with('message', '手机号添加成功!')
                ->with('message-type', 'success');
        }
        return Redirect::route('admin.notifications.index')
            ->with('message', '手机号已存在!')
            ->with('message-type', 'warning');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'notification_daily_money_threshold' => 'required|integer|between:1,100000000',
            'notification_daily_user_scan_threshold' => 'required|integer|between:1,10000',
            'notification_funding_pool_threshold' => 'required|integer|between:0,100000000',
        ], [
            'notification_daily_money_threshold.*' => '需填入1~100000000的整数',
            'notification_daily_user_scan_threshold.*' => '需填入1~10000的整数',
            'notification_funding_pool_threshold.*' => '需填入0~100000000的整数',
        ]);

        Settings::set('notification.threshold.daily_money_sum', $request->input('notification_daily_money_threshold'));
        Settings::set('notification.threshold.per_user_scan_count', $request->input('notification_daily_user_scan_threshold'));
        Settings::set('notification.threshold.funding_pool_balance', $request->input('notification_funding_pool_threshold'));

        return Redirect::route('admin.notifications.index')
            ->with('message', '触发阈值设置成功!')
            ->with('message-type', 'success');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function destroy(Request $request)
    {
        $telephone = $request->input('telephone');

        $phones_str = Settings::get('notification.phones', '');
        $phones = json_decode($phones_str, true);
        if (in_array($telephone, $phones)) {
            $phones = array_diff($phones, [$telephone]);
            Settings::set('notification.phones', json_encode($phones));
            return Redirect::route('admin.notifications.index')
                ->with('message', '手机号删除成功!')
                ->with('message-type', 'success');
        }
        return Redirect::route('admin.notifications.index')
            ->with('message', '手机号未找到!')
            ->with('message-type', 'warning');
    }
}
