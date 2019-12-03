<?php
namespace App\Http\Controllers\Admin;

use App\Distributor;
use App\Salesman;
use App\User;
use App\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use App\Shop;
use Maatwebsite\Excel\Facades\Excel;
use Mockery\CountValidator\Exception;

class ShopController extends Controller
{

    protected $shop;

    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $summary_date_range = $request->input('daterange');
        $summary_dates = explode(' - ', $summary_date_range);
        try {
            $summary_start_date = Carbon::parse($summary_dates[0]);
            $summary_end_date = Carbon::parse($summary_dates[1]);
        } catch (\Exception $e) {
            $summary_end_date = Carbon::today();
            $summary_start_date = $summary_end_date->copy()->subMonth(1)->addDay(1);
            $summary_date_range = implode(' - ', [$summary_start_date->format("Y-m-d"), $summary_end_date->format("Y-m-d")]);
            $request->merge(['daterange' => $summary_date_range]);
        }
        $filter_date_range = $request->input('filter_daterange');
        $filter_dates = explode(' - ', $filter_date_range);
        try {
            $filter_start_date = Carbon::parse($filter_dates[0]);
            $filter_end_date = Carbon::parse($filter_dates[1]);
        } catch (\Exception $e) {
            $filter_start_date = Carbon::minValue();
            $filter_end_date = Carbon::maxValue();
        }

        $request->flash();

        $filter_keyword = $request->input('filter_keyword');
        $filter_level = $request->input('filter_level');
        $filter_area_id = $request->input('filter_area_id');
        $filter_distributor_name = $request->input('filter_distributor');
        $filter_salesman_name = $request->input('filter_salesman');
        $filter_status = $request->input('filter_status');

        $shopsQuery = $this->shop->keyword($filter_keyword)
            ->withTrashed()
            ->filterStatus($filter_status)
            ->level($filter_level)
            ->area($filter_area_id)
            ->whereBetween('created_at', [$filter_start_date, $filter_end_date->endOfDay()])
            ->with(['category', 'distributor', 'area', 'owner', 'salesman', 'users.scanLog' => function ($query) use ($summary_start_date, $summary_end_date) {
                $query->whereBetween('created_at', [$summary_start_date, $summary_end_date->endOfDay()])
                    ->where('type', 'scan_prize');
            }])
            ->distributorName($filter_distributor_name)
            ->salesmanName($filter_salesman_name)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');
        //更新联系人
            // $contact_person = DB::table('contects')
            //     ->where('telephone','=','15800974557')
            //     ->get();
            // $contact_person = $contact_person['0']->name;
            // dd($contact_person);

        // $shopname = $shopsQuery->get()->toArray();
        // foreach ($shopname as $k => $v) {
        //     if ($v['contact_phone']) {
        //         $contact_person = DB::table('contects')
        //         ->where('telephone','=',$v['contact_phone'])
        //         ->get();
        //         if ($contact_person) {
        //             $contact_person = $contact_person['0']->name;
        //             DB::table('shops')
        //                 ->where('contact_phone','=',$v['contact_phone'])
        //                 ->update(['contact_person' => $contact_person]);
        //             # code...
        //         }
        //     }
        // }
        // dd($shopname);



        if ($request->input('export') === 'xls') {
            $shops = $shopsQuery->get();
            Excel::create('终端列表', function ($excel) use ($shops) {
                $excel->sheet('Sheet', function ($sheet) use ($shops) {
                    $sheet->loadView('admin.shop.index_xls')
                        ->with('shops', $shops);
                });
            })->export('xlsx');
        }
        $shops = $shopsQuery->paginate(50);


        return view('admin.shop.index',
            [
                'shops'      => $shops,
                'has_filter' => strlen($filter_keyword) > 0 || strlen($filter_level) > 0 || strlen($filter_area_id) > 0 || strlen($filter_distributor_name) > 0 || strlen($filter_date_range) > 0 || strlen($filter_salesman_name) > 0 || strlen($filter_status) > 0,
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
        $categories = Category::pluck('name', 'id');
        return view('admin.shop.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateForm($request);

        $shop = $this->shop->withTrashed()->create($request->except(['_token', 'logo_url']));
        if ($shop->id != 0) {
            return Redirect::route('admin.shops.index')
                ->with('message', '终端创建成功!')
                ->with('message-type', 'success');
        }
        return Redirect::route('admin.shops.create')
            ->withInput()
            ->withErrors($shop->errors());
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $date_range = $request->input('daterange');
        $dates = explode(' - ', $date_range);
        try {
            $start_date = Carbon::parse($dates[0]);
            $end_date = Carbon::parse($dates[1]);
        } catch (\Exception $e) {
            $end_date = Carbon::today();
            $start_date = $end_date->copy()->subMonth(1)->addDay(1);
            $date_range = implode(' - ', [$start_date->format("Y-m-d"), $end_date->format("Y-m-d")]);
            $request->merge(['daterange' => $date_range]);
        }
        $request->flash();

        $shop = $this->shop->withTrashed()->find($id);
        $users = User::where('shop_id', $id)
            ->with(['scanLog' => function ($query) use ($start_date, $end_date) {
                $query->whereBetween('created_at', [$start_date, $end_date->endOfDay()])
                ->where('type', 'scan_prize');
            }])
            ->get();

        if ($request->input('export') === 'xls') {
            Excel::create($shop->name, function ($excel) use ($shop, $users) {
                $excel->sheet('Sheet', function ($sheet) use ($shop, $users) {
                    $sheet->loadView('admin.shop.xls')
                        ->with('users', $users)
                        ->with('shop', $shop);
                });
            })->export('xlsx');
        }

        return view('admin.shop.show', [
            'users' => $users,
            'shop'  => $shop,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $shop = $this->shop->withTrashed()->findOrFail($id);
        return view('admin.shop.edit', [
            'waiters'  => User::where('shop_id', $id)->pluck('name', 'id'),
            'shop'  => $shop,
            'categories' => Category::pluck('name', 'id'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validateForm($request, $id);

        $param = $request->except(['_token', '_method', 'logo_url']);
        if (! $request->logo) {
            $param = $request->except(['_token', '_method', 'logo']);
        }

        $shop = $this->shop->withTrashed()->find($id);
        if ($shop == null) {
            return Redirect::route('admin.shops.index')
                ->with('message', '终端不存在!')
                ->with('message-type', 'error');
        }

        $isUpdated = $shop->update($param);
        if ($isUpdated) {
            return Redirect::route('admin.shops.index')
                ->with('message', '终端修改成功!')
                ->with('message-type', 'success');
        }
        return Redirect::route('admin.shops.update', $id)
            ->withInput()
            ->withErrors($shop->errors());
    }

    public function destroy($id)
    {
        $shop = $this->shop->withTrashed()->find($id);
        if ($shop == null) {
            return Redirect::back()
                ->with('message', '终端不存在!')
                ->with('message-type', 'error');
        }

        $isDeleted = $shop->delete();
        return Redirect::back()
            ->with('message', $isDeleted ? '终端已禁用!' : '终端禁用失败!')
            ->with('message-type', $isDeleted ? 'success' : 'error');
    }

    /**
     * Restore a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        $shop = $this->shop->onlyTrashed()->find($id);
        if (! $shop) {
            return Redirect::back()
                ->with('message', '终端不存在!')
                ->with('message-type', 'error');
        }

        $restore = $shop->restore();

        if (! $restore) {
            return Redirect::back()
                ->with('message', '终端状态修改失败!')
                ->with('message-type', 'error');
        }

        return Redirect::back()
            ->with('message', '修改终端状态成功!')
            ->with('message-type', 'success');
    }

    /**
     * excel数据导入
     * @param Request $request
     * @return mixed
     */
    public function import(Request $request)
    {
        header("Content-type: text/html; charset=utf-8");
        if (!$request->hasFile('excel')) {
            die('未选择EXCEL文件!');
        }

        $excel = $request->file('excel');
        $file = $excel->getRealPath();
        if(!$excel->isValid()){
            die('EXCEL文件上传失败!');
        }

        Excel::load($file, function($reader) use( &$data ) {
            $reader = $reader->getSheet(0);
            $data = $reader->toArray();

            $insert_data= [];
            foreach($data as $item) {
                unset($item[4]);
                $insert_data[] = $item;
            }

            foreach($insert_data as $value) {

                $app_id = DB::table('apps')->insertGetId([
                    'name' => $value[0],
                    'instruction' => $value[1],
                    'member_id' => 1,
                    'slogan' => $value[2],
                ]);

                $categories = explode(',', $value[3]);
                if ($app_id > 0 && count($categories) > 0)
                {
                    foreach($categories as $category) {
                        $app_id = DB::table('app_category')->insertGetId([
                            'app_id' => $app_id,
                            'category_id' => $category,
                        ]);
                    }
                }
            }

            echo 'success';die;



            try {
                if (count($data[0]) !== 10) {
                    $message = '标题行有多余数据，请删除后重试！';
                    throw new Exception($message);
                }

                // 1. VALIDATE EXCEL TITLE DATA IS NOT OK
                if (! $this->validateExcelTitle($data[0]) )
                {
                    throw new Exception('标题行格式错误!');
                }

                // 2. VALIDATE EXCEL HAS NO DATA
                if (! (count($data) > 1)) {
                    throw new Exception('EXCEL文件无数据!');
                }

                // 3. VALIDATE DATA TYPE IS NOT OK
                $updateDataArr = [];
                $insertDataArr = [];

                foreach ($data as $key=>$value)
                {
                    if ($key === 0) continue;

                    if (count($value) !== 10) {
                        $message = $key+1 . '行有多余数据，请删除后重试！';
                        throw new Exception($message);
                    }

                    $value[0] = (integer)$value[0];
                    if (isset($value[0]) && is_int($value[0]) && $value[0] > 0) {     // 修改的数据
                        $type = 'update';
                        $updateDataArr[] = $value;
                    } elseif ($value[0] === 0 || !isset($value[0])) {                    // 添加的数据
                        $type = 'insert';
                        $insertDataArr[] = $value;
                    } else {
                        throw new Exception($key+1 . '行数据格式错误，请修改后重试！');
                    }

                    $backData = $this->validateDataType($value, $type);

                    if ($backData['code'] === 400) {
                        throw new Exception($key+1 . '行数据格式错误，请修改后重试！错误信息：'.$backData['message']);
                    }
                }

                // 4. UPDATE DATA
                $updateErrorData = [];
                if (count($updateDataArr) > 0)       // 更新数据
                {
                    foreach ($updateDataArr as $key=>$value)
                    {
                        $res = Shop::where('id', $value[0])
                                    ->update([
                                        'name' => $value[1],
                                        'level' => $value[2],
                                        'distributor_id' => $value[3],
                                        'owner_id' => $value[4],
                                        'category_id' => $value[5],
                                        'salesman_id' => $value[6],
                                        'per_consume' => $value[7],
                                        'contact_person' => $value[8],
                                        'contact_phone' => $value[9],
                                    ]);
                        if (!$res) {
                            $updateErrorData[] = $value;
                        }
                    }
                }

                $insertErrorData = [];
                $repeatErrorData = [];
                if (count($insertDataArr) > 0)       // 添加数据
                {
                    foreach ($insertDataArr as $key=>$value)
                    {
                        $check = Shop::where('name', $value[1])->first();
                        if (!$check) {
                            $shopObj = new Shop();
                            $shopObj->name = $value[1];
                            $shopObj->level = $value[2];
                            $shopObj->distributor_id = $value[3];
                            $shopObj->owner_id = $value[4];
                            $shopObj->category_id = $value[5];
                            $shopObj->salesman_id = $value[6];
                            $shopObj->per_consume = $value[7];
                            $shopObj->contact_person = $value[8];
                            $shopObj->contact_phone = $value[9];
                            $res = $shopObj->save();
                            if (!$res) {
                                $insertErrorData[] = $value;
                            }
                        } else {
                            $repeatErrorData[] = $value;
                        }
                    }
                }

                $updateCount = count($updateDataArr) - count($updateErrorData);
                $insertCount = count($insertDataArr) - count($insertErrorData) - count($repeatErrorData);
                $message = '更新成功' . $updateCount . '条数据;<br>';
                $message .= '更新失败' . count($updateErrorData) . '条数据;<br>';
                $message .= '插入成功' . $insertCount . '条数据;<br>';
                $message .= '插入失败' . count($insertErrorData) . '条数据;<br>';
                $message .= '重复数据' . count($repeatErrorData) . '条，不予插入。';
                die($message);
            } catch (Exception $e) {
                die($e->getMessage());
            }
        });
    }

    // 判断数据格式是否正确
    private function validateExcelTitle($data)
    {
        if ($data[0] === 'ID' && $data[1] === '名称' && $data[2] === '级别'
            && $data[3] === '终端ID' && $data[4] === '店长ID' && $data[5] === '餐饮类型ID'
            && $data[6] === '营销员ID' && $data[7] === '人均消费（元）' && $data[8] === '联系人'
            && $data[9] === '联系电话')
        {
            return true;
        }
        return false;
    }

    /**
     * 判断数据格式是否正确
     * @param $data
     * @return bool
     */
    private function validateDataType($data, $type)
    {
        if ($type == 'update') {
            if ($data[0] === null || $data[1] === null || $data[2] === null
                || $data[3] === null || $data[4] === null || $data[5] === null
                || $data[6] === null || $data[7] === null || $data[8] === null
                || $data[9] === null)
            {
                return [
                    'code' => 400,
                    'message' => '整行数据不能留空',
                ];
            }
        } elseif ($type == 'insert') {
            if ($data[1] === null || $data[2] === null
                || $data[3] === null || $data[4] === null || $data[5] === null
                || $data[6] === null || $data[7] === null || $data[8] === null
                || $data[9] === null)
            {
                return [
                    'code' => 400,
                    'message' => '整行数据不能留空',
                ];
            }
        }

        foreach ($data as $key=>$value)
        {
            if ($type === 'update' && $key === 0)       // 验证更新数据
            {
                $value = (integer)$value;
                if (! ($value > 0)) {
                    return [
                        'code' => 400,
                        'message' => 'ID不大于0',
                    ];
                }

                $shop = Shop::find($value);
                if (!$shop) {
                    return [
                        'code' => 400,
                        'message' => 'ID:未找到对应的终端信息',
                    ];
                }
            }

            if ($key > 0 && isset($value)) {
                if ($key === 1) { // 名称
                    $length = mb_strlen($value, 'utf8');
                    if (! ( $length > 0 && $length <= 15 ) ) {
                        return [
                            'code' => 400,
                            'message' => '名称:长度应该在0 - 15个字符之间',
                        ];
                    }
                }

                if ($key === 2) { // 级别
                    if (! in_array($value, ['A', 'B', 'C', 'D'])) {
                        return [
                            'code' => 400,
                            'message' => '级别:级别应该在A,B,C,D间其中一个',
                        ];
                    }
                }

                if ($key === 3) { // 终端ID
                    $value = (integer)$value;
                    if ($value > 0) {
                        $distributor = Distributor::find($value);
                        if (!$distributor) {
                            return [
                                'code' => 400,
                                'message' => '终端ID:未找到相对应的终端信息',
                            ];
                        }
                    }
                }

                if ($key === 4) { // 店长ID
                    $value = (integer)$value;
                    if ($value > 0) {
                        $user = User::find($value);
                        if (!$user) {
                            return [
                                'code' => 400,
                                'message' => '店长ID:未找到相对应的店长信息',
                            ];
                        }
                    }
                }

                if ($key === 5) { // 餐饮类型ID
                    $value = (integer)$value;
                    if ($value > 0) {
                        $category = Category::find($value);
                        if (!$category) {
                            return [
                                'code' => 400,
                                'message' => '餐饮类型ID:未找到相对应的餐饮类型信息',
                            ];
                        }
                    }
                }

                if ($key === 6) { // 营销员ID
                    $value = (integer)$value;
                    if ($value > 0) {
                        $salesman = Salesman::find($value);
                        if (!$salesman) {
                            return [
                                'code' => 400,
                                'message' => '营销员ID:未找到相对应的营销员信息',
                            ];
                        }
                    }
                }

                if ($key === 7) { // 人均消费（元）
                    if (! (is_int($value) || is_float($value)) ) {
                        return [
                            'code' => 400,
                            'message' => '人均消费（元）:数值应该为数字',
                        ];
                    }
                }

                if ($key === 8) { // 联系人
                    $length = mb_strlen($value, 'utf8');
                    if (! ( $length >0 && $length<=15 ) ) {
                        return [
                            'code' => 400,
                            'message' => '联系人:长度应该在0 - 15个字符之间',
                        ];
                    }
                }

                if ($key === 9) { // 联系电话
                    $value = (string)$value;
                    $length = mb_strlen($value, 'utf8');
                    if (! ( $length > 0 && $length<=13 ) ) {
                        return [
                            'code' => 400,
                            'message' => '联系电话:长度应该在1 - 13个字符之间',
                        ];
                    }
                }
            }
        }

        return [
            'code' => 200,
            'message' => '数据无误',
        ];
    }

    /**
     * 终端logo上传
     */
    public function upload(Request $request)
    {
        $this->validate($request, [
            'logo_url' => 'required|image|mimes:jpeg,jpg,bmp,png'
        ], [
            'logo_url.*'    => '图片文件必传，可接受jpeg,jpg,bmp,png格式的图片',
        ]);

        if ($request->hasFile('logo_url')) {
            $image = $request->file('logo_url');
            $filename = md5(time() . mt_rand(100, 999)) . '.' . $image->getClientOriginalExtension();
            $pathfile = public_path('uploads/shop_logo/');
            $path = public_path('uploads/shop_logo/' . $filename);
            if (! is_dir($pathfile)) {
                mkdir($pathfile, 0777, true);
            }
            $result = Image::make($image->getRealPath())->fit(400)->save($path);
            if ($result) {
                return [
                    'code' => 200,
                    'message' => 'logo上传成功！',
                    'data' => [
                        'logo' => '/uploads/shop_logo/' . $filename
                    ],
                ];
            }

            return [
                'code' => 400,
                'message' => 'logo上传失败，请重试！',
                'data' => null,
            ];
        }
    }

    /**
     * Validate form for store and update
     *
     * @param Request $request
     */
    protected function validateForm(Request $request, $id = null)
    {
        Validator::extendImplicit('unique_shop_name', function($attribute, $value, $parameters, $validator) {
            $shop = Shop::find($parameters[0]);
            $count = Shop::where('name', $value)
                ->count();
            if ($shop->name == $value) {
                return $count > 1 ? false : true;
            }
            return $count > 0 ? false : true;
        });
        if ($id !== null) {
            $this->validate($request, [
                'name'           => 'required|max:15|min:2|unique_shop_name:'.$id,
                'level'          => 'required',
                'distributor_id' => 'exists:distributors,id',
                'area_id'        => 'required|exists:areas,id',
                'address'        => 'required|min:2|max:100',
                'salesman_id'    => 'required|exists:salesmen,id',
                'contact_person' => 'required|min:1|max:15',
                'contact_phone'  => 'required',
                'category_id'    => 'required',
                'per_consume'    => 'required|numeric',
            ], [
                'name.required'         => '名称为必填项,请填入2-15位中英文字符',
                'name.max'              => '名称为必填项,请填入2-15位中英文字符',
                'name.min'              => '名称为必填项,请填入2-15位中英文字符',
                'name.unique_shop_name' => '名称已存在，请更换名称',
                'distributor_id.exists' => '终端未找到',
                'area_id.required'      => '地区为必填项',
                'area_id.exists'        => '该地址未找到',
                'address.required'      => '地址为必填项,请填入2-100个字符',
                'address.min'           => '地址为必填项,请填入2-100个字符',
                'address.max'           => '地址为必填项,请填入2-100个字符',
                'salesman_id.required'  => '营销员为必填项',
                'salesman_id.exists'    => '营销员未找到',
                'contact_person.*'      => '联系人为必填项,请填入1-15位中英文字符',
                'contact_phone.required'=> '联系方式为必填项目',
                'category_id.required'  => '请选择餐饮类型',
                'per_consume.required'  => '人均消费为必填项',
                'per_consume.numeric'   => '人均消费为数值类型',
            ]);
        } else {
            $this->validate($request, [
                'name'           => 'required|max:15|min:1|unique:shops,name',
                'level'          => 'required',
                'distributor_id' => 'exists:distributors,id',
                'area_id'        => 'required|exists:areas,id',
                'address'        => 'required|min:2|max:100',
                'salesman_id'    => 'required|exists:salesmen,id',
                'contact_person' => 'required|min:1|max:15',
                'contact_phone'  => 'required',
                'category_id'    => 'required',
                'per_consume'    => 'required|numeric',
            ], [
                'name.required'         => '名称为必填项,请填入1-15位中英文字符',
                'name.max'              => '名称为必填项,请填入1-15位中英文字符',
                'name.min'              => '名称为必填项,请填入1-15位中英文字符',
                'name.unique'           => '名称已存在，请更换名称',
                'distributor_id.exists' => '终端未找到',
                'area_id.required'      => '地区为必填项',
                'area_id.exists'        => '该地址未找到',
                'address.required'      => '地址为必填项,请填入2-100个字符',
                'address.min'           => '地址为必填项,请填入2-100个字符',
                'address.max'           => '地址为必填项,请填入2-100个字符',
                'salesman_id.required'  => '营销员为必填项',
                'salesman_id.exists'    => '营销员未找到',
                'contact_person.*'      => '联系人为必填项,请填入1-15位中英文字符',
                'contact_phone.required'=> '联系方式为必填项目',
                'category_id.required'  => '请选择餐饮类型',
                'per_consume.required'  => '人均消费为必填项',
                'per_consume.numeric'   => '人均消费为数值类型',
            ]);
        }
    }
}
