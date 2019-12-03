<?php

namespace App\Http\Controllers\Admin;

use App\Code;
use App\CodeBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use Maatwebsite\Excel\Facades\Excel;

class CodeBatchController extends Controller
{
    protected $codeBatch;

    public function __construct(CodeBatch $codeBatch)
    {
        $this->codeBatch = $codeBatch;
    }

    public function index()
    {
        $batches = $this->codeBatch
            ->orderBy('id', 'desc')
            ->paginate(50);

        return view('admin.codebatch.index', ['batches' => $batches]);
    }

    public function store(Request $request, Code $code)
    {
        $error = null;
        $this->validate($request, [
            'type' => 'required|in:normal,activity,miniapp',
            'batch' => 'required|min:4|max:50|unique:code_batches,name',
            'count' => 'required|integer|min:10|max:6000',
        ], [
            'batch.unique' => '批次号已存在',
            'batch.*'      => '名称为必填项,请填入4-50位中英文字符',
            'count.*'      => '生成数量为10-6000的整数',
        ]);

        $type = $request->input('type');
        $count = $request->input('count');
        $name = $request->input('batch');

        $batch = $this->codeBatch->create([
            'name'   => $name,
            'count'  => $count,
            'status' => 'frozen',
            'type' => $type,
        ]);

        if ($batch->id == 0) {
            return Redirect::back()
                ->withInput()
                ->withErrors($batch->errors());
        }

        for ($i = 0; $i < $count; ++$i) {
            do {
                $codes = $this->generateCode($type, 1);
                $isUnique = $this->checkCodesUnique($codes, $code);
            } while ($isUnique !== true);
            $code->create([
                'code'     => $codes[0],
                'batch_id' => $batch->id,
            ]);
        }
        return Redirect::back()
            ->with('message', '二维码创建成功!')
            ->with('message-type', 'success');
    }

    //导出数据
    public function export(Request $request, Code $code)
    {
        $id = $request->input('batch');
        $batch = $this->codeBatch->find($id);

        if ($batch == null) {
            return Redirect::route('admin.codebatches.index')
                ->with('message', '批次号不存在!')
                ->with('message-type', 'error');
        }
        //toBase()什么意思，得到相同批次的二维码
        $codes = $code->where('batch_id', '=', $id)->get()->toBase();
        //线面的不太懂；
        Excel::create($batch->name, function ($excel) use ($codes) {
            $excel->sheet('Sheet', function ($sheet) use ($codes) {
                $codes->map(function ($code) use ($sheet) {
                    $sheet->appendRow(['http://ma.mikwine.com/' . $code->code]);
                });

            });
        })->export('txt');
    }

    //更新状态
    public function updateStatus(Request $request, $id)
    {
        $batch = $this->codeBatch->find($id);

        if ($batch == null) {
            return Redirect::back()
                ->with('message', '该批次不存在!')
                ->with('message-type', 'error');
        }

        $validator = $this->getValidationFactory()->make($request->all(), [
            'status' => 'required|in:normal,frozen'
        ], [
            'status.*' => '状态为必填项',
        ]);

        if ($validator->fails()) {
            return Redirect::back()
                ->with('message', '批次状态修改有误!')
                ->with('message-type', 'error');
        }

        $isUpdated = $batch->update(['status' => $request->input('status')]);

        if ($isUpdated) {
            return Redirect::back()
                ->with('message', '批次状态修改成功!')
                ->with('message-type', 'success');
        }
        return Redirect::back()
            ->with('message', '批次状态修改有误!')
            ->with('message-type', 'error');
    }

    private function checkCodesUnique($codes, Code $code)
    {
        return $code->whereIn('code', $codes)->count() == 0;
    }

    private function generateCode($type = 'normal', $count)
    {
        $count = intval($count);
        if ($count <= 0) {
            return [];
        }

        $length = 0;
        if ($type == 'normal') {
            $length = 6;
        } elseif ($type == 'activity') {
            $length = 7;
        } elseif ($type == 'miniapp') {
            $length = 8;
        }

        $chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        $result = [];
        do {
            $code = '0000';
            if ($type == 'normal') {
                $code = '0000';
            } elseif ($type == 'activity') {
                $code = '00000';
            } elseif ($type == 'miniapp') {
                $code = '000000';
            }
            for ($i = 0; $i < $length; ++$i) {
                $code = $code . substr($chars, mt_rand(0, strlen($chars) - 1), 1);
            }
            $result[] = $code;
        } while (--$count > 0);

        return $result;
    }
}
