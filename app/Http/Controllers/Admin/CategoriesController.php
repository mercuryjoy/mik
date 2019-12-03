<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Shop;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Facades\Excel;

class CategoriesController extends Controller
{
    protected $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

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

        $categories = Category::keyword($filter_keyword)
            ->level($filter_level)
            ->whereBetween('created_at', [$start_date, $end_date->endOfDay()])
            ->with('parentCategory')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(50);

        return view('admin.categories.index',
            [
                'categories' => $categories,
                'has_filter' => strlen($filter_keyword) > 0 || strlen($filter_level) > 0 || strlen($date_range) > 0,
            ]
        );
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $this->validateForm($request);

        $request->merge(['level' => 1]);
        $category = $this->category->create($request->except(['_token']));

        if ($category->id != 0) {
            return Redirect::route('admin.categories.index')
                ->with('message', '餐饮类型创建成功!')
                ->with('message-type', 'success');
        }

        return Redirect::route('admin.categories.create')
            ->withInput()
            ->withErrors($category->errors());
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

        $filter_shop_name = $request->input('filter_shop_name');
        $filter_shop_id = $request->input('filter_shop_id');
        $category = $this->category->findOrFail($id);
        $shops = Shop::where('category_id', $id)
            ->keyword($filter_shop_name)
            ->shopId($filter_shop_id)
            ->with('category')
            ->paginate(50);

        if ($request->input('export') === 'xls') {
            Excel::create($category->name, function($excel) use($category, $shops) {
                $excel->sheet('Sheet', function($sheet) use($category, $shops) {
                    $sheet->loadView('admin.categories.xls')
                        ->with('shops', $shops)
                        ->with('category', $category);
                });
            })->export('xlsx');
        }

        return view('admin.categories.show', [
            'shops' => $shops,
            'category' => $category,
        ]);
    }

    public function edit($id)
    {
        $category = $this->category->find($id);
        return view('admin.categories.edit', ['category' => $category]);
    }

    public function update(Request $request, $id)
    {
        $this->validateForm($request, $id);

        $category = $this->category->find($id);
        if ($category == null) {
            return Redirect::route('admin.categories.index')
                ->with('message', '餐饮类型不存在!')
                ->with('message-type', 'error');
        }

        $isUpdated = $category->update($request->except(['_token']));
        if ($isUpdated) {
            return Redirect::route('admin.categories.index')
                ->with('message', '餐饮类型修改成功!')
                ->with('message-type', 'success');
        }
        return Redirect::route('admin.categories.update', $id)
            ->withInput()
            ->withErrors($category->errors());
    }

    public function destroy($id)
    {
        $category = $this->category->find($id);
        if ($category == null) {
            return Redirect::route('admin.categories.index')
                ->with('message', '餐饮类型不存在!')
                ->with('message-type', 'error');
        }

        $isDeleted = $category->delete();
        return Redirect::back()
            ->with('message', $isDeleted ? '餐饮类型已删除!' : '餐饮类型删除失败!')
            ->with('message-type', $isDeleted ? 'success' : 'error');
    }

    /**
     * Validate form for store and update
     *
     * @param Request $request
     */
    protected function validateForm(Request $request, $id = null)
    {
//        Validator::extendImplicit('no_parent_for_level_one', function($attribute, $value, $parameters, $validator) {
//            return !($value != null && $parameters[0] == 1);
//        });
//
//        Validator::extendImplicit('level_match', function($attribute, $value, $parameters, $validator) {
//            if ($value == null) return true;
//
//            $boolean = false;
//            $parent_category = Category::find($value);
//            if ($parent_category) {
//                $boolean = $parent_category->level < $parameters[0];
//            }
//            return $boolean;
//        });

        Validator::extendImplicit('unique_category_name', function($attribute, $value, $parameters, $validator) {
            $category = Category::find($parameters[0]);
            $count = Category::where('name', $value)
                ->count();
            if ($category->name == $value) {
                return $count > 1 ? false : true;
            }
            return $count > 0 ? false : true;
        });

        if (!$id) {
            $this->validate($request, [
                'name' => 'required|max:15|min:1|unique:categories,name',
//            'level' => 'required',
//            'parent_id' => 'no_parent_for_level_one:' . $request->input('level') . '|exists:categories,id|level_match:' . $request->input('level'),
            ], [
                'name.required' => '名称为必填项,请填入1-15位中英文字符',
                'name.min' => '名称为必填项,请填入1-30位中英文字符',
                'name.max' => '名称为必填项,请填入1-30位中英文字符',
                'name.unique' => '名称已存在',
//            'parent_id.exists' => '上级餐饮类型未找到',
//            'parent_id.no_parent_for_level_one' => '一级餐饮类型不应该有上级',
//            'parent_id.level_match' => '上级餐饮类型等级应高于该经销商',
            ]);
        } else {
            $this->validate($request, [
                'name' => 'required|max:15|min:1|unique_category_name:'.$id,
//            'level' => 'required',
//            'parent_id' => 'no_parent_for_level_one:' . $request->input('level') . '|exists:categories,id|level_match:' . $request->input('level'),
            ], [
                'name.required' => '名称为必填项,请填入1-15位中英文字符',
                'name.min' => '名称为必填项,请填入1-30位中英文字符',
                'name.max' => '名称为必填项,请填入1-30位中英文字符',
                'name.unique_category_name' => '名称已存在',
//            'parent_id.exists' => '上级餐饮类型未找到',
//            'parent_id.no_parent_for_level_one' => '一级餐饮类型不应该有上级',
//            'parent_id.level_match' => '上级餐饮类型等级应高于该经销商',
            ]);
        }
    }
}
