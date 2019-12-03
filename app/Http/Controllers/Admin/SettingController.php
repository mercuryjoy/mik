<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Efriandika\LaravelSettings\Facades\Settings;

class SettingController extends Controller
{
    protected $protectedSettingsConfig = [
        [
            'title' => '界面设置',
            'items' => [
                ['app.skin', 'blue', '界面主题配色', [
                    'black' => 'Black',
                    'blue' => 'Blue',
                    'blue-light' => 'Blue Light',
                    'green' => 'Green',
                    'green-light' => 'Green Light',
                    'purple' => 'Purple',
                    'purple-light' => 'Purple Light',
                    'red' => 'Red',
                    'red-light' => 'Red Light',
                    'yellow' => 'Yellow',
                    'yellow-light' => 'Yellow Light',
                    ]
                ],
            ],
        ],

        [
            'title' => '服务维护',
            'items' => [
                ['app.api_service.status', 'on', 'API状态', [
                    'on' => '服务中',
                    'off' => '维护中'
                ],
                ],
                ['app.withdraw_api_service.status', 'on', '提现API状态', [
                    'on' => '服务中',
                    'off' => '维护中'
                ],
                ],
            ],
        ],
        [
            'title' => '特殊帐号(Apple审核用)',
            'items' => [
                ['app.special_account.telephone', '13123456789', '手机号'],
                ['app.special_account.verify_code', '1234', '验证码'],
            ],
        ],
    ];

    protected $settingsConfig = [
        [
            'title' => '扫码配置',
            'items' => [
                // ['scan.point_per_scan', '20', '每次扫码获得积分数', 'number'],
                ['scan.money_to_waiter_first_scan', '1', '新用户扫码服务员收益金额（元）', 'number'],
                ['scan.point_to_waiter_first_scan', '1', '新用户扫码服务员收益积分数（分）', 'number'],
                ['scan.money_to_owner_waiter_scan', '1', '服务员扫码店长收益（元）', 'number'],
            ],
        ],

        [
            'title' => '红包兑换积分汇率配置',
            'items' => [
                ['app.money_to_point_exchange_rate', '100', '1元红包 = ? 积分', 'number'],
            ],
        ],

        [
            'title' => 'App最新版设置',
            'items' => [
                ['app.latest.ios.version', '', '最新iOS版本号', 'text'],
                ['app.latest.ios.download_url', '', '最新iOS版本下载地址', 'text'],
                ['app.latest.android.version', '', '最新Android版本号', 'text'],
                ['app.latest.android.download_url', '', '最新Android版本下载地址', 'text'],
            ],
        ],

    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $settingsGroup = $this->settingsConfig;

        if (Auth::User()->isSuperAdmin()) {
            $settingsGroup = array_merge($this->protectedSettingsConfig, $settingsGroup);
        }

        foreach ($settingsGroup as $i => $settings) {
            foreach ($settings['items'] as $j => $setting) {
                $settingsGroup[$i]['items'][$j][1] = Settings::get($setting[0], $setting[1]);
            }
        }

        return view('admin.setting.index', [
            'settingsGroup' => $settingsGroup
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $newSettings = $request->input('settings');
        if (is_array($newSettings)) {
            foreach ($newSettings as $key => $value) {
                Settings::set($key, $value);
            }
            return Redirect::route('admin.settings.index')
                ->with('message', '配置保存成功!')
                ->with('message-type', 'success');
        }
        return Redirect::route('admin.settings.index')
            ->with('message', '配置保存失败!')
            ->with('message-type', 'error');;
    }
}
