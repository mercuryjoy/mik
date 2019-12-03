<?php

namespace App\Http\Controllers\API;

use App\News;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
/**
 * @SWG\Tag(name="News", description="新闻")
 */
class NewsController extends APIController
{
    /**
     * @SWG\Get(
     *     path="/news",
     *     tags={"News"},
     *     summary="获取所有新闻",
     *     @SWG\Response(response="200", description="设置获取成功"),
     *     @SWG\Response(response="500", description="服务器内部错误"),
     * )
     */
    public function index(Request $request) {
        $uid = $request->user_id;
        $newsList = News::orderBy('id', 'desc')
            ->where('status','=','normal')->get();
        $new = $newsList->toArray();
        foreach ($new as $k => $v) {
            $newslog = DB::table('user_new_logs as l')
                ->where('l.new_id','=',$v['id'])
                ->where('l.user_id','=',$uid)
                ->get();
            if ($newslog) {
                $new[$k]['newslog']='read'; 
            }else{
                $new[$k]['newslog']='unread'; 
            }
        }
        return new JsonResponse($new);
    }

    public function newlog(Request $request) 
    {
        $this->validate($request, [
            'user_id' => 'required',
            'new_id' => 'required',
        ], [
            'user_id.required' => '101|user_id不能为空',
            'new_id.required' => '102|new_id不能为空',
        ]);

        $uid = $request->user_id;
        $nid = $request->new_id;

        $unlog = DB::table('user_new_logs as l')
                ->where('l.new_id','=',$nid)
                ->where('l.user_id','=',$uid)
                ->get();
        $showtime=date("Y-m-d H:i:s");
        if (!$unlog) {
            $id = DB::table('user_new_logs')
                ->insert(
                ['user_id' => $uid, 'new_id' => $nid,'created_at' => $showtime]
                );
            return new JsonResponse(['code' => 200, 'message' => '生成log已读']);
        }

        return new JsonResponse(['code' => 200, 'message' => 'log已经存在']);
    }
}
