<?php

namespace App\Http\Controllers\API;

use App\Helpers\Contracts\SMSContract;
use App\SMSLog;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Http\Requests;
use Illuminate\Support\Facades\Response;

/**
 * @SWG\Tag(name="SMS", description="短信验证")
 */
class SMSController extends APIController
{
    /**
     * @SWG\Post(
     *     path="/sms/code",
     *     tags={"SMS"},
     *     summary="发送验证短信",
     *     @SWG\Parameter(name="telephone", in="formData", required=true, type="string"),
     *     @SWG\Response(response="200", description="发送短信认证码")
     * )
     */
    public function code(Request $request, SMSContract $sms, User $userObj) {
        $this->validate($request, [
            'telephone' => 'required|regex:' . config('custom.telephone_regex'),
        ], [
            'telephone.required' => '101|手机号码不能为空',
            'telephone.regex' => '102|手机号码格式不正确',
        ]);

        //'wechat_bind'：微信绑定,'wechat_unbind'：微信解绑,'withdraw'：提现,'update_login_password'：更新登录密码,'update_withdraw_password'：更新提现密码
        $type = $request->input('type', 'verify_register');
        $telephone = $request->input('telephone');
        $code = mt_rand(1000, 9999);

        $allow_type = ['verify_register', 'verify_register_password', 'verify_reset_password', 'wechat_bind', 'wechat_unbind', 'withdraw', 'update_login_password', 'update_withdraw_password'];
        if (empty($type) || !in_array($type, $allow_type)) {
            return new JsonResponse($this->buildErrorResponse('103|验证码类型错误'), 400);
        }

        if ($type == 'verify_reset_password') {
            $user = $userObj->withTrashed()->where('telephone', $telephone)->first();

            if (! $user) {
                return new JsonResponse($this->buildErrorResponse('104|您的账号未注册，请去注册'), 400);
            }

            if ($user->trashed()) {
                return new JsonResponse($this->buildErrorResponse('105|您的账号已被禁用，请联系管理员'), 400);
            }
        }

        $sms->sendVerifyCode($request->input('telephone'), $code, $type);

        $codeObj = SMSLog::where('code', $code)
            ->where('telephone', $request->telephone)
            ->where('type', $type)
            ->where('status', 'sent')
            ->first();

        if ($codeObj) {
            return new JsonResponse(['code' => 200, 'message' => '短信发送成功']);
        } else {
            return new JsonResponse(['code' => 400, 'message' => '短信发送失败']);
        }
    }

    public function checkAuthCode(Request $request, SMSLog $smsLogRepo)
    {
        $this->validate($request, [
            'type' => 'required|in:wechat_bind,wechat_unbind,withdraw,update_login_password,update_withdraw_password',
            'code' => 'required|regex:/^[0-9]{4}$/',
        ], [
            'type.required' => '201|验证类型不能为空',
            'type.in' => '202|验证类型不正确',
            'code.required' => '203|验证码不能为空',
            'code.regex' => '204|验证码为4位数字',
        ]);

        $user = $request->user;

        if (! $user->telephone) {
            return new JsonResponse($this->buildErrorResponse('205|您暂未绑定手机号，请绑定'), 400);
        }

        $telephone = $user->telephone;
        $type = $request->input('type');
        $code = $request->input('code');

        $log = $smsLogRepo
            ->where('status', 'sent')
            ->where('type', $type)
            ->where('telephone', $telephone)
            ->orderBy('id', 'desc')
            ->first();

        if ($log == null) {
            return new JsonResponse($this->buildErrorResponse('206|未找到验证码'), 400);
        } else if ($log->code != $code) {
            return new JsonResponse($this->buildErrorResponse('207|验证码不正确'), 400);
        } else if ($log->created_at->diffInSeconds() > 60 * 5 ) {
            return new JsonResponse($this->buildErrorResponse('208|验证码已过期'), 400);
        }

        $log->update(['status' => 'used']);

        return new JsonResponse(['code' => 200, 'message' => '验证码正确']);
    }

    public function checkVerifyResetPasswordCode(Request $request, SMSLog $smsLogRepo, User $user)
    {
        $this->validate($request, [
            'telephone' => 'required|regex:' . config('custom.telephone_regex'),
            'code' => 'required|regex:/^[0-9]{4}$/',
        ], [
            'telephone.required' => '201|手机号码不能为空',
            'telephone.regex' => '202|手机号码不正确',
            'code.required' => '203|验证码不能为空',
            'code.regex' => '204|验证码为4位数字',
        ]);

        $telephone = $request->input('telephone');
        $code = $request->input('code');

        $log = $smsLogRepo
            ->where('status', 'sent')
            ->where('type', 'verify_reset_password')
            ->where('telephone', $telephone)
            ->orderBy('id', 'desc')
            ->first();

        if ($log == null) {
            return new JsonResponse($this->buildErrorResponse('205|未找到验证码'), 400);
        } else if ($log->code != $code) {
            return new JsonResponse($this->buildErrorResponse('206|验证码不正确'), 400);
        } else if ($log->created_at->diffInSeconds() > 60 * 5 ) {
            return new JsonResponse($this->buildErrorResponse('207|验证码已过期'), 400);
        }

        $log->update(['status' => 'used']);

        return new JsonResponse(['code' => 200, 'message' => '验证码正确']);
    }
}
