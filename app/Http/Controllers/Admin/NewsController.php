<?php

namespace App\Http\Controllers\Admin;

use App\News;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Intervention\Image\Facades\Image;

class NewsController extends Controller
{
    private $news;

    public function __construct(News $news)
    {
        $this->news = $news;
    }

    public function index() {
        $newsList = $this->news
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('admin.news.index', ['news_list' => $newsList]);
    }

    public function create(Request $request)
    {
       return view('admin.news.create');
    }

    public function store(Request $request)
    {
        //dd($request->hasFile('picture'));
        if($request->hasFile('picture')) {
            $image = $request->file('picture');
            //getClientOriginalExtension上传文件的后缀；
            $filename  = time() . '.' . $image->getClientOriginalExtension();
            //public_path函数返回public目录的绝对路径;
            $path = public_path('newspics/' . $filename);
            //Image::make找不到
            //页面添加能成功，但是一直报错，有重名，没找到错误在哪；开始没有开PHPfile扩展，打开扩展报错；
            Image::make($image->getRealPath())->fit(80)->save($path);
            $request->merge(['picture_url' => '/newspics/' . $filename]);
        }
        //dd($request->hasFile('thumbnail'));
        if($request->hasFile('thumbnail')) {
            $image = $request->file('thumbnail');
            //getClientOriginalExtension上传文件的后缀；
            $filename  = time() . '.' . $image->getClientOriginalExtension();
            //public_path函数返回public目录的绝对路径;
            $path = public_path('newspics/' . $filename);
            //Image::make找不到
            //页面添加能成功，但是一直报错，有重名，没找到错误在哪；开始没有开PHPfile扩展，打开扩展报错；
            Image::make($image->getRealPath())->fit(400,240)->save($path);
            $request->merge(['thumbnail_url' => '/newspics/' . $filename]);
        }


        $this->validateForm($request);
        $news = $this->news->create($request->except(['_token']));
        if ($news->id != 0) {
            return Redirect::route('admin.news.index')
                ->with('message', '新闻创建成功!')
                ->with('message-type', 'success');
        }
        return Redirect::route('admin.news.create')
            ->withInput()
            ->withErrors($news->errors());
    }

    public function edit($id)
    {
        $news = $this->news->findOrFail($id);

        return view('admin.news.edit', ['news' => $news]);
    }

    public function update(Request $request, $id)
    {
        $item = $this->news->find($id);
        if ($item == null) {
            return Redirect::route('admin.news.index')
                ->with('message', '新闻不存在!')
                ->with('message-type', 'error');
        }
        // if ($request->hasFile('audio_url')) {
//             $apk = $request->file('audio');
//             // dd($apk);
//             // if ($apk->isValid()) {
//                 $apkSavePath = config('android_apk_path', 'uploads/packages/');
//                 $newFileName = rand(1000, 9999) . '.' . $apk->getClientOriginalExtension();
//                 $absolutelyPath = public_path($apkSavePath);
//                 // dd($absolutelyPath);
//                 if (!is_dir($absolutelyPath)) {
//                     mkdir($absolutelyPath, 0777, true);
//                 }
// //                $apk->move($absolutelyPath, $newFileName);
//                 $request->merge(['audio_url' => '/'.$apkSavePath.$newFileName]);
            // }
        // }
        if($request->hasFile('picture')) {
            $image = $request->file('picture');
            $filename  = time() . '.' . $image->getClientOriginalExtension();
            $path = public_path('newspics/' . $filename);

            Image::make($image->getRealPath())->fit(80)->save($path);
            $request->merge(['picture_url' => '/newspics/' . $filename]);
        }
        if($request->hasFile('thumbnail')) {
            $image = $request->file('thumbnail');
            $filename  = time() . '.' . $image->getClientOriginalExtension();
            $path = public_path('newspics/' . $filename);

            Image::make($image->getRealPath())->fit(400,240)->save($path);
            $request->merge(['thumbnail_url' => '/newspics/' . $filename]);
        }

        $this->validateForm($request);
        $isUpdated = $item->update($request->except(['_token']));
        if ($isUpdated) {
            return Redirect::route('admin.news.index')
                ->with('message', '新闻修改成功!')
                ->with('message-type', 'success');
        }
        return Redirect::route('admin.news.edit', $id)
            ->withInput()
            ->withErrors($item->errors());
    }

    public function destroy($id)
    {
        $news = $this->news->find($id);
        if ($news == null) {
            return Redirect::route('admin.news.index')
                ->with('message', '删除的新闻不存在!')
                ->with('message-type', 'error');
        }

        $isDeleted = $news->delete();
        return Redirect::back()
            ->with('message', $isDeleted ? '新闻已删除!' : '新闻删除失败!')
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
            // 'content_url' => 'required|max:1000|min:1',
        ], [
            'title.*' => '标题为必填项,请填入2-50位中英文字符',
            // 'content_url.*' => '新闻链接为必填项',
        ]);
    }
}
