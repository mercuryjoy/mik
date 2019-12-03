<?php

namespace App\Http\Controllers\Admin;

use App\DrawRule;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class DrawRuleController extends Controller
{
    private $drawRule;

    public function __construct(DrawRule $drawRule)
    {
        $this->drawRule = $drawRule;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rules = $this->drawRule
            ->with('area', 'distributor', 'shop')
            ->get();

        return view('admin.drawrule.index', ['rules' => $rules]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if (!in_array($request->input('type'), ['area', 'distributor', 'shop'])) {
            return Redirect ::back()
                ->with('message','无法识别规则类型!')
                ->with('message-type', 'error');
        }

        return view('admin.drawrule.create', ['type' => $request->input('type')]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateForm($request, null);

        $rule = $this->drawRule->create($request->except(['_token']));

        if ($rule->id != 0) {
            return Redirect::route('admin.drawrules.index')
                ->with('message', '规则创建成功!')
                ->with('message-type', 'success');
        }
        return Redirect::route('admin.drawrules.create')
            ->withInput()
            ->withErrors($rule->errors());
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
        $rule = $this->drawRule->findOrFail($id);

        return view('admin.drawrule.edit', ['rule' => $rule]);
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
        $drawRule = $this->drawRule->find($id);
        if ($drawRule == null) {
            return Redirect::route('admin.drawrules.index')
                ->with('message', '规则不存在!')
                ->with('message-type', 'error');
        }

        $this->validateForm($request, $drawRule);

        $isUpdated = $drawRule->update($request->except(['_token']));
        if ($isUpdated) {
            return Redirect::route('admin.drawrules.index')
                ->with('message', '规则修改成功!')
                ->with('message-type', 'success');
        }
        return Redirect::route('admin.drawrules.update', $id)
            ->withInput()
            ->withErrors($drawRule->errors());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $drawRule = $this->drawRule->find($id);
        if ($drawRule == null) {
            return Redirect::route('admin.drawrules.index')
                ->with('message', '删除的规则不存在!')
                ->with('message-type', 'error');
        }

        if ($drawRule->ruleType == 'base') {
            return Redirect::route('admin.drawrules.index')
                ->with('message', '全局规则不能删除!')
                ->with('message-type', 'error');
        }

        $isDeleted = $drawRule->delete();
        return Redirect::back()
            ->with('message', $isDeleted ? '规则已删除!' : '规则删除失败!')
            ->with('message-type', $isDeleted ? 'success' : 'error');
    }

    private function validateForm(Request $request, $rule) {
        Validator::extendImplicit('rule_json_format', function($attribute, $value, $parameters, $validator) {
            $data = json_decode($value);
            if (!is_array($data)) return false;
            foreach ($data as $rule ) {
                if (!is_object($rule)) return false;
                if (!isset($rule->percentage)) return false;
                if (!isset($rule->min)) return false;
                if (!isset($rule->max)) return false;
                if ($rule->max <= 0 || $rule->min > $rule->max) return false;
            }
            return true;
        });

        Validator::extendImplicit('rule_json_sum', function($attribute, $value, $parameters, $validator) {
            $data = json_decode($value);
            if (!is_array($data)) return false;
            $sum = 0;
            foreach ($data as $rule ) {
                if (!is_object($rule)) return false;
                if (!isset($rule->percentage)) return false;
                $sum += $rule->percentage;
            }
            return $sum == 100;
        });

        if ($rule != null && $rule->ruleType == 'base') {
            $this->validate($request, [
                'rule_json' => 'required|json|rule_json_format|rule_json_sum',
            ], [
                'rule_json.required' => '规则数据有误',
                'rule_json.json' => '规则数据有误',
                'rule_json.rule_json_format' => '规则数据有误',
                'rule_json.rule_json_sum' => '规则占比总和须为100%',
            ]);
        } else {
            $this->validate($request, [
                'area_id' => 'required_without_all:distributor_id,shop_id|exists:areas,id|unique:draw_rules,area_id' . ($rule ? ',' . $rule->id : ''),
                'distributor_id' => 'required_without_all:area_id,shop_id|exists:distributors,id|unique:draw_rules,distributor_id' . ($rule ? ',' . $rule->id  : ''),
                'shop_id' => 'required_without_all:distributor_id,area_id|exists:shops,id|unique:draw_rules,shop_id' . ($rule ? ',' . $rule->id : ''),
                'rule_json' => 'required|json|rule_json_format|rule_json_sum',
            ], [
                'area_id.required_without_all' => '地区为必填项',
                'area_id.exists' => '该地区未找到',
                'area_id.unique' => '该地区规则已经存在',
                'distributor_id.required_without_all' => '经销商为必填项',
                'distributor_id.exists' => '该经销商未找到',
                'distributor_id.unique' => '该经销商规则已经存在',
                'shop_id.required_without_all' => '终端为必填项',
                'shop_id.exists' => '该终端未找到',
                'shop_id.unique' => '该终端规则已经存在',
                'rule_json.required' => '规则数据有误',
                'rule_json.json' => '规则数据有误',
                'rule_json.rule_json_format' => '规则数据有误',
                'rule_json.rule_json_sum' => '规则占比总和须为100%',
            ]);
        }
    }
}
