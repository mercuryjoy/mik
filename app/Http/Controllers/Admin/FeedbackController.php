<?php

namespace App\Http\Controllers\Admin;

use App\Feedback;
use App\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Http\Requests;
use App\Reply;
use DB;

class FeedbackController extends Controller
{
    protected $feedback;

    public function __construct(Feedback $feedback)
    {
        $this->feedback = $feedback;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Feedback $feedback , Request $request)
    {
        $date_range = $request->input('filter_daterange');
        $dates = explode(' - ', $date_range);
        try {
            $start_date = Carbon::parse($dates[0]);
            $end_date = Carbon::parse($dates[1]);
        } catch(\Exception $e) {
            $start_date = Carbon::minValue();
            $end_date = Carbon::maxValue();
        }
        $request->flash();
        $filter_status = $request->input('status');
        $filter_user_keyword = $request->input('filter_user_keyword');
        $filter_phone_keyword = $request->input('filter_phone_keyword');
        $feedbacks = $feedback
                    // ->withTrashed()
                    ->userName($filter_user_keyword)
                    ->userPhone($filter_phone_keyword)
                    ->status($filter_status)
                    ->whereBetween('created_at', [$start_date, $end_date->endOfDay()])
                    ->with('user')
                    ->orderBy('id', 'desc')
                    ->paginate(50);

        $feedback = DB::table('feedbacks as f')
            ->leftJoin('users as u', 'f.user_id', '=', 'u.id')
            ->leftJoin('shops as s','u.shop_id','=','s.id')
            ->leftJoin('salesmen as sm','s.salesman_id','=','sm.id')
            ->get(['u.id','u.name','u.telephone','f.content','s.name as sname','sm.name as smname','f.status']);

        if ($request->input('export') === 'allxls') {
            Excel::create('经销商列表'.date("Y-m-d"), function($excel) use($feedback) {
                $excel->sheet('Sheet', function($sheet) use($feedback) {
                    $sheet->loadView('admin.feedback.allxls')
                        ->with('feedback', $feedback);
                });
            })->export('xlsx');
        }

        return view('admin.feedback.index', ['feedback' => $feedback,'feedbacks' => $feedbacks]);
    }
    public function show($id)
    {
        $feedback = $this->feedback->withTrashed()->find($id);

        return view('admin.feedback.show', ['feedback' => $feedback]);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $feedback = $this->feedback->withTrashed()->find($id);
        $feedbacks = DB::table('feedbacks as f')
            ->leftJoin('users as u', 'f.user_id', '=', 'u.id')
            ->leftJoin('shops as s','u.shop_id','=','s.id')
            ->leftJoin('salesmen as sm','s.salesman_id','=','sm.id')
            ->where('f.id','=',$id)
            ->get(['s.name as sname','sm.name as smname']);
        $reply = Reply::where('feedback_id','=',$id)
                ->orderBy('id', 'desc')
                ->get();         

        return view('admin.feedback.edit', ['feedback' => $feedback ,'feedbacks' => $feedbacks['0'] ,'reply' => $reply]);
    }

    public function destroy($id)
    {
        $feedbacks = $this->feedback->find($id);
        if ($feedbacks == null) {
            return Redirect::route('admin.feedbacks.index')
                ->with('message', '删除的新闻不存在!')
                ->with('message-type', 'error');
        }

        $isDeleted = $feedbacks->delete();
        return Redirect::back()
            ->with('message', $isDeleted ? '新闻已删除!' : '新闻删除失败!')
            ->with('message-type', $isDeleted ? 'success' : 'error');
    }


}
