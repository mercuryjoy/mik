<?php

namespace App\Http\Controllers\Admin;

use App\Shop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use App\Distributor;
use Maatwebsite\Excel\Facades\Excel;
use DB;
use GuzzleHttp\Client;
/**
 * Class DistributorController
 * @package App\Http\Controllers\Admin
 */
class DistributorController extends Controller
{

    protected $distributor;

    public function __construct(Distributor $distributor)
    {
        $this->distributor = $distributor;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $date_range = $request->input('daterange');
        $dates = explode(' - ', $date_range);
        try {
            $start_date = Carbon::parse($dates[0]);
            $end_date = Carbon::parse($dates[1]);
        } catch(\Exception $e) {
            $start_date = Carbon::minValue();
            $end_date = Carbon::maxValue();
        }

        $request->flash();

        $filter_keyword = $request->input('keyword');
        $filter_level = $request->input('level');
        $filter_area_id = $request->input('area_id');
        $filter_status = $request->input('filter_status');

        $distributors = Distributor::keyword($filter_keyword)
            ->withTrashed()
            ->level($filter_level)
            ->area($filter_area_id)
            ->filterStatus($filter_status)
            ->whereBetween('created_at', [$start_date, $end_date->endOfDay()])
            ->with('parent_distributor', 'area')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(50);

        $distributor = Distributor::keyword($filter_keyword)
            ->withTrashed()
            ->level($filter_level)
            ->area($filter_area_id)
            ->filterStatus($filter_status)
            ->whereBetween('created_at', [$start_date, $end_date->endOfDay()])
            ->with('parent_distributor', 'area')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        if ($request->input('export') === 'allxls') {
            Excel::create('经销商列表'.date("Y-m-d"), function($excel) use($distributor) {
                $excel->sheet('Sheet', function($sheet) use($distributor) {
                    $sheet->loadView('admin.distributor.allxls')
                        ->with('distributor', $distributor);
                });
            })->export('xlsx');
        }

        return view('admin.distributor.index',
            [
                'distributor' => $distributor,
                'distributors' => $distributors,
                'has_filter' => strlen($filter_keyword) > 0 || strlen($filter_level) > 0 || strlen($filter_area_id) > 0 || strlen($date_range) > 0 || strlen($filter_status) > 0,
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.distributor.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateForm($request);

        $distributor = $this->distributor->create($request->except(['_token']));

        if ($distributor->id != 0) {
            return Redirect::route('admin.distributors.index')
                ->with('message', '经销商创建成功!')
                ->with('message-type', 'success');
        }
        return Redirect::route('admin.distributors.create')
            ->withInput()
            ->withErrors($distributor->errors());
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $filter_shop_id = $request->input('filter_shop_id');
        $filter_keyword = $request->input('filter_keyword');
        $date_range = $request->input('daterange');
        $dates = explode(' - ', $date_range);
        try {
            $start_date = Carbon::parse($dates[0]);
            $end_date = Carbon::parse($dates[1]);
        } catch(\Exception $e) {
            $end_date = Carbon::today();
            $start_date = $end_date->copy()->subMonth(1)->addDay(1);
            $date_range = implode(' - ', [$start_date->format("Y-m-d"), $end_date->format("Y-m-d")]);
            $request->merge(['daterange' => $date_range]);
        }
        $request->flash();

        $distributor = $this->distributor->withTrashed()->find($id);

        $shops = Shop::where('distributor_id', $id)
            ->shopId($filter_shop_id)
            ->keyword($filter_keyword)
            ->with(['users', 'users.scanLog' => function($query) use ($start_date, $end_date) {
                $query->where('type', 'scan_prize')->whereBetween('created_at', [$start_date, $end_date->endOfDay()]);
            }])
            ->get();
        if ($request->input('export') === 'xls') {
            Excel::create($distributor->name, function($excel) use($distributor, $shops) {
                $excel->sheet('Sheet', function($sheet) use($distributor, $shops) {
                    $sheet->loadView('admin.distributor.xls')
                        ->with('shops', $shops)
                        ->with('distributor', $distributor);
                });
            })->export('xlsx');
        }

        return view('admin.distributor.show', [
            'shops' => $shops,
            'distributor' => $distributor,
        ]);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $distributor = $this->distributor->withTrashed()->find($id);
        return view('admin.distributor.edit', ['distributor' => $distributor]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validateForm($request);
        // dump($id);

        // dd($request->level);
        //利用GuzzleHttp调用外部api接口
        // $client = new \GuzzleHttp\Client();

        // $r = $client->request('POST', 'http://app-dev.mikwine.com/api/news/newlog', [
        //     'form_params' => [
        // 'user_id' => '2',
        // 'new_id' => '3'
        //     ]
        // ]);

        $distributor = $this->distributor->withTrashed()->find($id);
        if ($distributor == null) {
            return Redirect::route('admin.distributors.index')
                ->with('message', '经销商不存在!')
                ->with('message-type', 'error');
        }

        $isUpdated = $distributor->update($request->except(['_token']));
        if ($isUpdated) {
            return Redirect::route('admin.distributors.index')
                ->with('message', '经销商修改成功!')
                ->with('message-type', 'success');
        }
        return Redirect::route('admin.distributors.update', $id)
            ->withInput()
            ->withErrors($distributor->errors());
    }

    public function destroy($id)
    {
        $distributor = $this->distributor->withTrashed()->find($id);
        if ($distributor == null) {
            return Redirect::back()
                ->with('message', '经销商不存在!')
                ->with('message-type', 'error');
        }

        $isDeleted = $distributor->delete();
        return Redirect::back()
            ->with('message', $isDeleted ? '经销商已禁用!' : '经销商禁用失败!')
            ->with('message-type', $isDeleted ? 'success' : 'error');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        $distributor = $this->distributor->onlyTrashed()->find($id);
        if (! $distributor) {
            return Redirect::back()
                ->with('message', '经销商不存在!')
                ->with('message-type', 'error');
        }

        $restore = $distributor->restore();

        if (! $restore) {
            return Redirect::back()
                ->with('message', '经销商状态修改失败!')
                ->with('message-type', 'error');
        }

        return Redirect::back()
            ->with('message', '修改经销商状态成功!')
            ->with('message-type', 'success');
    }

    /**
     * Validate form for store and update
     *
     * @param Request $request
     */
    protected function validateForm(Request $request) {
        Validator::extendImplicit('no_parent_for_level_one', function($attribute, $value, $parameters, $validator) {
            return !($value != null && $parameters[0] == 1);
        });

        Validator::extendImplicit('level_match', function($attribute, $value, $parameters, $validator) {
            if ($value == null) return true;
            $parent_distributor = Distributor::find($value);
            if ($parent_distributor) {
                return $parent_distributor->level < $parameters[0];
            }
        });

        $this->validate($request, [
            'name' => 'required|max:15|min:1',
            'level' => 'required',
            'parent_distributor_id' => 'no_parent_for_level_one:' . $request->input('level') . '|exists:distributors,id|level_match:' . $request->input('level'),
            'area_id' => 'required|exists:areas,id',
            'address' => 'max:100',
            'contact' => 'max:20',
            'telephone' => 'max:30'
        ], [
            'name.required' => '名称为必填项,请填入1-15位中英文字符',
            'name.min' => '名称为必填项,请填入4-30位中英文字符',
            'name.max' => '名称为必填项,请填入4-30位中英文字符',
            'parent_distributor_id.exists' => '上级经销商未找到',
            'parent_distributor_id.no_parent_for_level_one' => '一级经销商不应该有上级',
            'parent_distributor_id.level_match' => '上级经销商等级应高于该经销商',
            'area_id.required' => '地区为必填项',
            'area_id.exists' => '该地区未找到',
            'address.max' => '地址最多100个字符',
            'contact.max' => '联系人最多20个字符',
            'telephone.max' => '联系电话最多30个字符',
        ]);
    }
}
