<?php

namespace App\Http\Controllers\Admin;

use App\Banners;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Intervention\Image\Facades\Image;

class BannersController extends Controller
{
    private $banners;

    public function __construct(Banners $banners)
    {
        $this->banners = $banners;
    }

    public function index() {
        $bannersList = $this->banners
            ->orderBy('order_id', 'ASC')
            ->orderBy('id', 'ASC')
            ->paginate(20);
        return view('admin.banners.index', ['banners_list' => $bannersList]);
    }

    public function create(Request $request)
    {
       return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        //dd($request->hasFile('thumbnail'));
        if($request->hasFile('thumbnail')) {
            $image = $request->file('thumbnail');
            //getClientOriginalExtension上传文件的后缀；
            $filename  = time() . '.' . $image->getClientOriginalExtension();
            //public_path函数返回public目录的绝对路径;
            $path = public_path('bannerspics/' . $filename);
            //Image::make找不到
            //页面添加能成功，但是一直报错，有重名，没找到错误在哪；开始没有开PHPfile扩展，打开扩展报错；
            Image::make($image->getRealPath())->resize(749, 331)->save($path);
            $request->merge(['thumbnail_url' => '/bannerspics/' . $filename]);
        }


        // $this->validateForm($request);
        $banners = $this->banners->create($request->except(['_token']));
        if ($banners->id != 0) {
            return Redirect::route('admin.banners.index')
                ->with('message', '轮播图创建成功!')
                ->with('message-type', 'success');
        }
        return Redirect::route('admin.banners.create')
            ->withInput()
            ->withErrors($banners->errors());
    }

    public function edit($id)
    {
        $banners = $this->banners->findOrFail($id);
        return view('admin.banners.edit', ['banners' => $banners]);
    }

    public function update(Request $request, $id)
    {
        $item = $this->banners->find($id);
        if ($item == null) {
            return Redirect::route('admin.banners.index')
                ->with('message', '轮播图不存在!')
                ->with('message-type', 'error');
        }

        if($request->hasFile('thumbnail')) {
            $image = $request->file('thumbnail');
            $filename  = time() . '.' . $image->getClientOriginalExtension();
            $path = public_path('bannerspics/' . $filename);

            Image::make($image->getRealPath())->resize(1920, 800)->save($path);
            $request->merge(['thumbnail_url' => '/bannerspics/' . $filename]);
        }

        // $this->validateForm($request);
        $isUpdated = $item->update($request->except(['_token']));
        if ($isUpdated) {
            return Redirect::route('admin.banners.index')
                ->with('message', '轮播图修改成功!')
                ->with('message-type', 'success');
        }
        return Redirect::route('admin.banners.edit', $id)
            ->withInput()
            ->withErrors($item->errors());
    }

    public function destroy($id)
    {
        $banners = $this->banners->find($id);
        if ($banners == null) {
            return Redirect::route('admin.banners.index')
                ->with('message', '删除的轮播图不存在!')
                ->with('message-type', 'error');
        }

        $isDeleted = $banners->delete();
        return Redirect::back()
            ->with('message', $isDeleted ? '轮播图已删除!' : '轮播图删除失败!')
            ->with('message-type', $isDeleted ? 'success' : 'error');
    }

    /**
     * Validate form for store and update
     *
     * @param Request $request
     */
    protected function validateForm(Request $request) {
        $this->validate($request, [
            'title' => 'required|max:50|min:2',
            'content_url' => 'required|max:1000|min:1',
        ], [
            'title.*' => '标题为必填项,请填入2-50位中英文字符',
            'content_url.*' => '轮播图链接为必填项',
        ]);
    }
}
