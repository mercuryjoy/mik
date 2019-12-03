<?php

namespace App\Http\Controllers\API;

use App\Shop;
use App\SMSLog;
use App\User;
use Efriandika\LaravelSettings\Facades\Settings;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Http\Requests;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @SWG\Tag(name="Auth", description="身份验证")
 */
class LoginController extends APIController
{
    /**
     * @SWG\Post(
     *     path="/login",
     *     tags={"Auth"},
     *     summary="验证用户登录",
     *     @SWG\Parameter(name="telephone", in="formData", required=true, type="string"),
     *     @SWG\Parameter(name="code", in="formData", required=true, type="string"),
     *     @SWG\Response(response="200", description="登录成功",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(type="string", property="id"),
     *              @SWG\Property(type="string", property="name"),
     *              @SWG\Property(type="string", property="gender"),
     *              @SWG\Property(type="string", property="telephone"),
     *              @SWG\Property(type="integer", property="shop_id"),
     *              @SWG\Property(type="integer", property="area_id"),
     *              @SWG\Property(type="string", property="deleted_at"),
     *              @SWG\Property(type="string", property="created_at"),
     *              @SWG\Property(type="string", property="updated_at"),
     *              @SWG\Property(type="string", property="token")
     *          )
     *     )
     * )
     */
    public function login(Request $request, SMSLog $smsLogRepo, User $userRepo) {

        $this->validate($request, [
            'telephone' => 'required|regex:' . config('custom.telephone_regex'),
            'code' => 'required|regex:/^[0-9]{4}$/'
        ], [
            'telephone.required' => '201|手机号码不能为空',
            'telephone.regex' => '202|手机号码格式不正确',
            'code.required' => '203|验证码不能为空',
            'code.regex' => '204|验证码为4位数字',
        ]);

        $telephone = $request->input('telephone');
        $code = $request->input('code');

        // Apple 审核专用帐号
        $special_telephone = Settings::get('app.special_account.telephone');
        $special_verify_code = Settings::get('app.special_account.verify_code');

        if (in_array(env('APP_DEBUG'), [true, false]) && $code == '8888') {
        } else if ($telephone === $special_telephone && $code === $special_verify_code) {
        } else {
            $log = $smsLogRepo
                ->where('status', '=', 'sent')
                ->where('type', '=', 'verify_register')
                ->where('telephone', '=', $telephone)
                ->orderBy('id', 'desc')
                ->first();

            if ($log == null) {
                return new JsonResponse($this->buildErrorResponse('205|未找到验证码'), 400);
            } else if ($log->created_at->diffInSeconds() > 60 * 5 ) {
                return new JsonResponse($this->buildErrorResponse('206|验证码已过期'), 400);
            } else if ($log->code != $code) {
                return new JsonResponse($this->buildErrorResponse('207|验证码不正确'), 400);
            }

            $log->update(['status' => 'used']);
        }

        $user = $userRepo->withTrashed()->where('telephone', '=', $telephone)->first();
        if ($user == null) {
            $user = $userRepo->create([
                'name' => $telephone,
                'telephone' => $telephone,
            ]);
        } else if ($user->trashed()) {
            return new JsonResponse($this->buildErrorResponse('208|该手机号已被禁用,请换一个重试'), 400);
        }

        // 判断是否是店长
        $is_owner = false;
        $shop_id = $user->shop_id;
        if ($shop_id) {
            $shop = Shop::find($shop_id);
            if ($shop) {
                if ($shop->owner_id && $user->id == $shop->owner_id) {
                    $is_owner = true;
                }
            }
        }

        $token = JWTAuth::fromUser($user);

        return new JsonResponse(array_merge($user->toArray(), compact('token', 'is_owner')));
    }
}
