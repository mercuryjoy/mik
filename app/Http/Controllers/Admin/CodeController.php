<?php

namespace App\Http\Controllers\Admin;


use App\Code;
use Illuminate\Http\Request;
class CodeController extends Controller
{
    protected $code;

    public function __construct(Code $code)
    {
        $this->code = $code;
    }

    public function index(Request $request)
    {
        $filter_code = $request->input('code');
        $filter_keyword = $request->input('keyword');
        $filter_batch_keyword = $request->input('filter_batch_keyword');
        $codes = $this->code
            ->keyword($filter_keyword)
            ->with('batch')
            ->batchName($filter_batch_keyword)
            ->code($filter_code)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(50);
        return view('admin.code.index', [
            'codes' => $codes
        ]);
    }
}
