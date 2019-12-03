<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Reply;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use App\Feedback;
use DB;
use App\User;
class RepliesController extends Controller
{
    
    private $replies;


    public function __construct(Reply $replies)
    {
        $this->replies = $replies;
    }

    public function index() {
        $replist = $this->replies
            ->with('user')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('admin.replies.index', ['replist' => $replist]);
    }

    public function create(Request $request)
    {
       return view('admin.replies.create');
    }

    public function show ($id)
    {

        $feedback = DB::table('feedbacks as f')
                ->leftJoin('users as u', 'f.user_id', '=', 'u.id')
                ->where('f.id','=',$id)
                ->get(['f.id','f.content','f.created_at','u.name']);

        return view('admin.replies.show', ['feedback' => $feedback]);
    }

    public function store(Request $request ,Reply $reply)
    {
       
        $reply->content = $request->content;

        $reply->user_id = $request->user_id;

        $fid=$reply->feedback_id = $request->feedback_id;

        $reply->save();
        
        DB::table('feedbacks')
            ->where('id', $fid)
            ->update(['status' => 'reply']);
        return Redirect::route('admin.feedbacks.index')
                ->with('message', '回复成功!')
                ->with('message-type', 'success');

    }
}