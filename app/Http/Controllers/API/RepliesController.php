<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Reply;
use App\Feedback;
use DB;
use App\Shop;
use App\Salesmen;
use App\Banners;
class RepliesController extends APIController
{
    public function fbshow(Request $request)
    {
        //根据user_id查询
        $this->validate($request, [
            'user_id' => 'required|regex:/(^[\-0-9][0-9]*(.[0-9]+)?)$/'
        ], [
            'user_id.required' => '101|user_id为数字不能为空',
            'user_id.regex' => '102|user_id为数字',
        ]);

        $uid =$request->user_id;
        $fbsObj = Feedback::where('user_id', $uid)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        $feedback = $fbsObj->toArray();

        if (empty($feedback)) {
            return new JsonResponse($this->buildErrorResponse('400|没有消息内容'), 400);
        }
        return new JsonResponse($feedback);

    }

    public function replyshow(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|regex:/(^[\-0-9][0-9]*(.[0-9]+)?)$/'
        ], [
            'user_id.required' => '101|user_id为数字不能为空',
            'user_id.regex' => '102|user_id为数字',
        ]);

        $uid =$request->user_id;
        $fbsObj = Feedback::where('user_id', $uid)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        $feedback = $fbsObj->toArray();
        if (empty($feedback)) {
                return new JsonResponse($this->buildErrorResponse('400|没有消息内容'), 400);
            }
        foreach ($feedback as $k => $v) {
            $replies = DB::table('replies as r')
                ->where('r.feedback_id','=',$v['id'])
                ->get();

            $feedback[$k]['replies']=$replies;    
        }
        return new JsonResponse($feedback);

        //根据通知id查询回复
        /*
        $this->validate($request, [
            'feedback_id' => 'required|regex:/(^[\-0-9][0-9]*(.[0-9]+)?)$/'
        ], [
            'feedback_id.required' => '101|feedback_id为数字不能为空',
            'feedback_id.regex' => '102|feedback_id为数字',
        ]);

        $fid = $request->feedback_id;

        // $pageSize = $request->pageSize ? $request->pageSize : 20;

        $repliesObj = Reply::where('feedback_id', $fid)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();
            // ->paginate($pageSize);
        $reply = $repliesObj->toArray();
        if (empty($reply)) {
            return new JsonResponse($this->buildErrorResponse('400|没有回复内容'), 400);
        }
        return new JsonResponse($reply);
        */
    }
    public function banners(Request $request)
    {
        $bannersList = DB::table('banners')
               ->orderBy('order_id', 'ASC')
               ->orderBy('id', 'ASC')
               ->get();
        return new JsonResponse($bannersList);
    }

}
