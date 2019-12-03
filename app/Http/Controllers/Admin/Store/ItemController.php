<?php

namespace App\Http\Controllers\Admin\Store;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use Intervention\Image\Facades\Image;

use App\Http\Requests;
use App\Http\Controllers\Admin\Controller;

use App\StoreItem;

class ItemController extends Controller
{
    protected $storeItem;

    public function __construct(StoreItem $storeItem)
    {
        $this->storeItem = $storeItem;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->flash();

        $filter_status = $request->input('status');

        $items = $this->storeItem
            ->status($filter_status)
            ->where('type', 'exchange')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('admin.store.item.index', [
            'items' => $items,
            'has_filter' => strlen($filter_status) > 0,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.store.item.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->hasFile('photo')) {
            $image = $request->file('photo');
            $filename  = time() . '.' . $image->getClientOriginalExtension();
            $path = public_path('storepics/' . $filename);

            Image::make($image->getRealPath())->fit(400)->save($path);
            $request->merge(['photo_url' => '/storepics/' . $filename]);
        }

        $this->validateForm($request);

        $fields = $request->except(['_token']);
        $fields['price_money'] = floatval($fields['price_money']) * 100;
        $item = $this->storeItem->create($fields);

        if ($item->id != 0) {
                return Redirect::route('admin.store.items.index')
                ->with('message', '商品创建成功!')
                ->with('message-type', 'success');
        }
        return Redirect::route('admin.store.items.create')
            ->withInput()
            ->withErrors($item->errors());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = $this->storeItem->findOrFail($id);
        return view('admin.store.item.edit', ['item' => $item]);
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
        $item = $this->storeItem->find($id);
        if ($item == null) {
            return Redirect::route('admin.store.items.index')
                ->with('message', '商品不存在!')
                ->with('message-type', 'error');
        }

        if($request->hasFile('photo')) {
            $image = $request->file('photo');
            $filename  = time() . '.' . $image->getClientOriginalExtension();
            $path = public_path('storepics/' . $filename);

            Image::make($image->getRealPath())->fit(400)->save($path);
            $request->merge(['photo_url' => '/storepics/' . $filename]);
        }

        $this->validateForm($request);

        $fields = $request->except(['_token']);
        $fields['price_money'] = floatval($fields['price_money']) * 100;
        $isUpdated = $item->update($fields);

        if ($isUpdated) {
            return Redirect::route('admin.store.items.index')
                ->with('message', '商品修改成功!')
                ->with('message-type', 'success');
        }
        return Redirect::route('admin.store.items.edit', $id)
            ->withInput()
            ->withErrors($item->errors());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Validate form for store and update
     *
     * @param Request $request
     */
    protected function validateForm(Request $request) {
        $this->validate($request, [
            'name' => 'required|max:30|min:2',
            'price_point' => 'required|integer|min:1',
            'price_money' => 'numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:in_stock,out_of_stock',
        ], [
            'name.*' => '名称为必填项,请填入2-30位中英文字符',
            'price_point.*' => '需要积分数,请填入大于0的整数',
            'price_money.*' => '需要红包金额,请填入大于0的数',
            'stock.*' => '库存为必填项，请填入一个 >=0 的整数',
            'status.*' => '状态为必填项',
        ]);
    }

}
