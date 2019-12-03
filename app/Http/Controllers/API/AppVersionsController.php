<?php

namespace App\Http\Controllers\API;

use App\AppVersion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @SWG\Tag(name="Version", description="版本升级")
 */
class AppVersionsController extends APIController
{
    /**
     * @SWG\Get(
     *     path="/app/versions",
     *     tags={"Version"},
     *     summary="APP版本升级",
     *     @SWG\Parameter(name="type", in="query", required=true, type="string"),
     *     @SWG\Response(response="200", description="成功")
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function version(Request $request)
    {
        $this->validate($request, [
            'type' => 'required|in:android,ios'
        ], [
            'type.required' => '平台类型不能为空。',
            'type.in' => '平台类型必须为特定平台值。',
        ]);

        $appVersion = AppVersion::where('type', $request->type)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        if ($appVersion) {
            if ($appVersion->version_code && $appVersion->version_code !== null) {
                $appVersion->version_code = config('custom.app_url').$appVersion->version_code;
            }
            if ($appVersion->type && $appVersion->type === 'android') {
                $appVersion->download_url = config('custom.app_url').$appVersion->download_url;
            }
        }

        return new JsonResponse($appVersion, 200);
    }
}
