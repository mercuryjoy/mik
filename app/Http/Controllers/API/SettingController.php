<?php

namespace App\Http\Controllers\API;

use Efriandika\LaravelSettings\Facades\Settings;
use Illuminate\Http\JsonResponse;

use App\Http\Requests;

/**
 * @SWG\Tag(name="Settings", description="设置")
 */
class SettingController extends APIController
{
    /**
     * @SWG\Get(
     *     path="/settings",
     *     tags={"Settings"},
     *     summary="获取所有设置",
     *     @SWG\Response(response="200", description="设置获取成功"),
     *     @SWG\Response(response="500", description="服务器内部错误"),
     * )
     */
    public function index() {
        $settings = [
            'app.latest.ios.version' => '',
            'app.latest.ios.download_url' => '',
            'app.latest.android.version' => '',
            'app.latest.android.download_url' => '',
            'app.money_to_point_exchange_rate' => '100',
        ];

        foreach ($settings as $key => $default) {
            $settings[$key] = Settings::get($key, $default);
        }

        return new JsonResponse($settings);
    }
}
