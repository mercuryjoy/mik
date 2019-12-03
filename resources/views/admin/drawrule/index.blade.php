@extends('admin.layouts.app')

@section('htmlheader_title')
    中奖设置
@endsection

@section('contentheader_title')
    中奖设置
@endsection

@section('main-content')
    <div class='row'>
        <div class='col-md-12'>
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">新建规则</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <a class="btn btn-primary margin" href="{{ route("admin.drawrules.create", ['type' => 'area']) }}">新建地区规则</a>
                    <a class="btn btn-primary margin" href="{{ route("admin.drawrules.create", ['type' => 'distributor']) }}">新建经销商规则</a>
                    <a class="btn btn-primary margin" href="{{ route("admin.drawrules.create", ['type' => 'shop']) }}">新建终端规则</a>
                </div>
            </div>
        </div>
    </div>

    <div class='row'>
        <div class='col-md-12'>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">中奖规则</h3>
                </div>
                <div class="box-body table-responsive">
                    <div class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-hover dataTable" role="grid">
                                    <thead>
                                    <tr role="row">
                                        <th>类型</th>
                                        <th>规则目标</th>
                                        <th>操作</th>
                                    </thead>
                                    <tbody>
                                    @foreach($rules as $rule)
                                        <tr role="row">
                                            <td>{{ $rule->ruleTypeDisplay }}</td>

                                            <td>
                                                @if($rule->ruleType == 'base')
                                                    基础规则
                                                @elseif($rule->ruleType == 'shop')
                                                    @if($rule->shop != null)
                                                        <a href="{{ route('admin.shops.edit', ['id' => $rule['shop_id']]) }}">{{$rule->shop->name}}</a>
                                                    @else
                                                        [已删除终端]
                                                    @endif
                                                @elseif($rule->ruleType == 'distributor')
                                                    @if($rule->shop != null)
                                                        <a href="{{ route('admin.distributors.edit', ['id' => $rule['distributor_id']]) }}">{{$rule->distributor->name}}</a>
                                                    @else
                                                        [已删除经销商]
                                                    @endif
                                                @elseif($rule->ruleType == 'area')
                                                    {{$rule->area->display}}
                                                @endif
                                            </td>

                                            <td>
                                                <a class="btn btn-xs btn-primary" href="{{ route('admin.drawrules.edit', ['id' => $rule->id]) }}">详情/修改</a>
                                                @if($rule->ruleType != 'base')
                                                    <a class="btn btn-xs btn-danger" href="#" data-href="{{ route('admin.drawrules.destroy', ['id' => $rule->id]) }}" data-toggle="modal" data-target="#confirm-delete">删除</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-danger fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    删除中奖规则
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    您是否确认删除该规则? 此操作无法恢复, 请慎用!
                </div>
                <div class="modal-footer">
                    {{ Form::open(array('method' => 'delete', 'id' => 'deleteRuleForm')) }}
                    <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-outline btn-ok">删除</button>
                    {{ Form::close() }}
                    </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    $('#confirm-delete').on('show.bs.modal', function(e) {
        $(this).find('#deleteRuleForm').attr('action', $(e.relatedTarget).data('href'));
    });
@endsection
