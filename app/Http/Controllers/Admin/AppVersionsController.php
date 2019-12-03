<?php

namespace App\Http\Controllers\Admin;

use App\AppVersion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AppVersionsController extends Controller
{
    private $appVersion;

    public function __construct(AppVersion $appVersion)
    {
        $this->appVersion = $appVersion;
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

        $filter_type = $request->input('type');
        $filter_is_force_update = $request->input('is_force_update');

        $versions = $this->appVersion->type($filter_type)
            ->isForceUpdate($filter_is_force_update)
            ->whereBetween('created_at', [$start_date, $end_date->endOfDay()])
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(50);

        return view('admin.version.index',
            [
                'versions' => $versions,
                'has_filter' => strlen($filter_type) > 0 || strlen($filter_is_force_update) > 0 || strlen($date_range) > 0,
            ]
        );
    }

    public function create()
    {
        return view('admin.version.create');
    }

    public function store(Request $request)
    {
        $this->validateForm($request);

        // 1.if type is android, Then upload the apk file
        if ($request->type == 'android' && $request->hasFile('android_file')) {
            $apk = $request->file('android_file');
            if ($apk->isValid()) {
                $apkSavePath = config('android_apk_path', 'uploads/packages/');
                $newFileName = $request->type . '-' . $request->version . '-' . rand(1000, 9999) . '.' . $apk->getClientOriginalExtension();
                $absolutelyPath = public_path($apkSavePath);
                if (!is_dir($absolutelyPath)) {
                    mkdir($absolutelyPath, 0777, true);
                }
//                $apk->move($absolutelyPath, $newFileName);
                $request->merge(['download_url' => '/'.$apkSavePath.$newFileName]);
            }
        }

        // 2.if has download_url, Then translate download_url to qr_code
        if ($request->has('download_url')) {
            $codeSavePath = config('app_qr_code_path', 'uploads/codes/');
            $versionCode = $codeSavePath . $request->type . '-' . $request->version . '-' . rand(1000, 9999) . '.png';
            if (!is_dir(public_path($codeSavePath))) {
                mkdir(public_path($codeSavePath), 0777, true);
            }
            if ($request->type == 'android') {
                $downloadUrl = config('custom.app_url'). $request->download_url;
            } elseif ($request->type == 'ios') {
                $downloadUrl = $request->download_url;
            }
            QrCode::format('png')->size(300)->generate($downloadUrl, public_path($versionCode));
            $request->merge(['version_code' => '/' . $versionCode]);
        }

        // 3.insert data into table
        if ($request->has('version_code')) {
            $appVersion = $this->appVersion->create($request->except(['_token']));
            if ($appVersion->id != 0) {
                return Redirect::route('admin.versions.index')
                    ->with('message', 'APP版本创建成功!')
                    ->with('message-type', 'success');
            }

            return Redirect::route('admin.versions.create')
                ->withInput()
                ->with('message', 'APP版本创建失败,请重试!')
                ->with('message-type', 'error')
                ->withErrors($appVersion->errors());
        }

        return Redirect::route('admin.versions.create')
            ->withInput()
            ->with('message', '数据有误,请重试!')
            ->with('message-type', 'error');
    }

    public function destroy($id)
    {
        $version = $this->appVersion->find($id);
        if ($version == null) {
            return Redirect::route('admin.versions.index')
                ->with('message', 'APP版本信息不存在!')
                ->with('message-type', 'error');
        }

        $isDeleted = $version->delete();
        return Redirect::back()
            ->with('message', $isDeleted ? 'APP版本信息已删除!' : 'APP版本信息删除失败!')
            ->with('message-type', $isDeleted ? 'success' : 'error');
    }

    public function edit($id)
    {
        $version = $this->appVersion->findOrFail($id);
        return view('admin.version.edit', ['version' => $version]);
    }

    public function update(Request $request, $id)
    {
        $this->validateForm($request, $id);

        $appVersion = $this->appVersion->find($id);

        if ($appVersion == null) {
            return Redirect::route('admin.versions.index')
                ->with('message', 'APP版本不存在!')
                ->with('message-type', 'error');
        }

        // 1.if type is android, Then upload the apk file
        if ($request->type == 'android' && $request->hasFile('android_file')) {
            $apk = $request->file('android_file');
            if ($apk->isValid()) {
                $apkSavePath = config('android_apk_path', 'uploads/packages/');
                $newFileName = $request->type . '-' . $request->version . '-' . rand(1000, 9999) . '.' . $apk->getClientOriginalExtension();
                $absolutelyPath = public_path($apkSavePath);
                if (!is_dir($absolutelyPath)) {
                    mkdir($absolutelyPath, 0777, true);
                }
               // $apk->move($absolutelyPath, $newFileName);
                $request->merge(['download_url' => '/'.$apkSavePath.$newFileName]);
            }
        }

        // 2.if has download_url, Then translate download_url to qr_code
        if ($request->has('download_url')) {
            $codeSavePath = config('app_qr_code_path', 'uploads/codes/');
            $versionCode = $codeSavePath . $request->type . '-' . $request->version . '-' . rand(1000, 9999) . '.png';
            if (!is_dir(public_path($codeSavePath))) {
                mkdir(public_path($codeSavePath), 0777, true);
            }
            if ($request->type == 'android') {
                $downloadUrl = config('custom.app_url'). $request->download_url;
            } elseif ($request->type == 'ios') {
                $downloadUrl = $request->download_url;
            }
            QrCode::format('png')->size(300)->generate($downloadUrl, public_path($versionCode));
            $request->merge(['version_code' => '/' . $versionCode]);
        }


        $isUpdated = $appVersion->update($request->except(['_token', '_method', 'android_file']));
        if ($isUpdated) {
            return Redirect::route('admin.versions.index')
                ->with('message', 'APP版本修改成功!')
                ->with('message-type', 'success');
        }
        return Redirect::route('admin.versions.edit', $id)
            ->withInput()
            ->withErrors($appVersion->errors());
    }

    /**
     * Validate form for store and update
     *
     * @param Request $request
     */
    protected function validateForm(Request $request, $id = null)
    {
        if ($id === null) {
            $this->validate($request, [
                'type' => 'required|in:android,ios',
                'version' => 'required',
                'download_url' => 'required_if:type,ios',
                'description' => 'required|min:4|max:50',
                'is_force_update' => 'required|in:yes,no',
                'android_file' => 'required_if:type,android',
            ], [
                'type.required' => '平台类型为必填项',
                'type.in' => '平台类型有误',
                'version.required' => '版本号为必填项',
                'download_url.required_if' => 'Ios下载地址为必填项',
                'description.required' => '版本描述为必填项,请填入4-50位中英文字符',
                'description.min' => '版本描述为必填项,请填入4-50位中英文字符',
                'description.max' => '版本描述为必填项,请填入4-50位中英文字符',
                'is_force_update.required' => '强制升级为必选项',
                'is_force_update.in' => '强制升级数据有误',
                'android_file.required_if' => '安卓必须上传安装包',
            ]);
        } else {
            $this->validate($request, [
                'type' => 'required|in:android,ios',
                'version' => 'required',
                'download_url' => 'required_if:type,ios',
                'description' => 'required|min:4|max:50',
                'is_force_update' => 'required|in:yes,no',
            ], [
                'type.required' => '平台类型为必填项',
                'type.in' => '平台类型有误',
                'version.numeric' => '版本号必须为数字',
                'download_url.required_if' => 'Ios下载地址为必填项',
                'description.required' => '版本描述为必填项,请填入4-50位中英文字符',
                'description.min' => '版本描述为必填项,请填入4-50位中英文字符',
                'description.max' => '版本描述为必填项,请填入4-50位中英文字符',
                'is_force_update.required' => '强制升级为必选项',
                'is_force_update.in' => '强制升级数据有误',
            ]);
        }
    }
}
