<?php

namespace App\Http\Controllers\API;

use App\Feedback;
use Illuminate\Http\Request;

use App\Http\Requests;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @SWG\Tag(name="Feedback", description="用户反馈")
 */
class FeedbackController extends APIController
{

    /**
     * @SWG\Post(
     *     path="/feedbacks",
     *     tags={"Feedback"},
     *     summary="发送反馈",
     *     @SWG\Parameter(name="content", in="formData", required=true, type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     * @return JsonResponse
     */

    public function store(Feedback $feedback, Request $request) {
        // 1. validate input
        $this->validate($request, [
            'content' => 'required|min:1',
        ], [
            'content.*' => '501|反馈内容不能为空',
        ]);

        // create
        Feedback::create([
            'user_id' => $request->user->id,
            'content' => $request->input('content')
        ])->save();

        return new JsonResponse([], 200);
    }
}
