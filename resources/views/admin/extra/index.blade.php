@extends('admin.layouts.app')

@section('htmlheader_title')
    扫码额外收入记录
@endsection

@section('contentheader_title')
    扫码额外收入记录
@endsection

@section('main-content')
    <div class='row'>
        <div class='col-md-12'>
            {!! Form::open(['route' => ['admin.extra.index'], 'method' => 'get']) !!}
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">过滤条件</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('daterange', '时间段') !!}
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    {!! Form::text('daterange', old('daterange'), ['class' => 'daterange form-control']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('type', '收益类型') !!}
                                {!! Form::select('type', ["" => "全部收益类型", "waiter"=> "服务员收益", "owner" => "店长收益"], old('type'), ['class' => 'form-control select2']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('scan_type', '扫码类型') !!}
                                {!! Form::select('scan_type', ["" => "全部扫码类型", "waiter_user_scan_over"=> "服务员和用户已扫", "waiter_owner_scan_over" => "服务员已扫发店长红包", /*"waiter_scan_user_no" => "服务员已扫用户未扫", "user_scan_waiter_no" => "服务员未扫用户已扫"*/], old('scan_type'), ['class' => 'form-control select2']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('code', '二维码') !!}
                                {!! Form::text('code', old('code'), ['class' => 'form-control', 'placeholder' => '二维码']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('user_name', '关键字(服务员名)') !!}
                                {!! Form::text('user_name', old('user_name'), ['class' => 'form-control', 'placeholder' => '服务员名']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('filter_salesman', '关键字(销售员姓名)') !!}
                                {!! Form::text('filter_salesman', old('filter_salesman'), ['class' => 'form-control', 'placeholder' => '销售员姓名']) !!}
                            </div>
                        </div><div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('shop_name', '关键字(终端名称)') !!}
                                {!! Form::text('shop_name', old('shop_name'), ['class' => 'form-control', 'placeholder' => '终端名称']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('distribution_name', '关键字(经销商名称)') !!}
                                {!! Form::text('distribution_name', old('distribution_name'), ['class' => 'form-control', 'placeholder' => '经销商名称']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button class="btn btn-primary" type="submit"><i class="fa fa-filter"></i> 应用过滤条件</button>
                    <a class="btn btn-warning pull-right" href="{{ route("admin.extra.index") }}"><i class="fa fa-trash"></i> 清除过滤条件</a>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

    <div class='row'>
        <div class='col-md-12'>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">扫码额外收入记录{{ $has_filter ? "(已过滤)" : "" }}</h3>
                    <div class="box-tools pull-right">
                        <a href="{{ route('admin.extra.index', array_merge(app('request')->all(), ['export' => 'xls']))}}" class="btn btn-primary" target="_blank"><i class="fa fa-file-excel-o"></i> 导出Excel</a>
                    </div>
                </div>
                <div class="box-body table-responsive">
                    <div class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-hover dataTable" role="grid">
                                    <thead>
                                    <tr role="row">
                                        <th>ID</th>
                                        <th>二维码</th>
                                        <th>类型</th>
                                        <th>服务员</th>
                                        <th>用户ID</th>
                                        <th>用户</th>
                                        <th>店长</th>
                                        <th>终端名称</th>
                                        <th>收益(元)</th>
                                        <th>积分收益(积分)</th>
                                        <th>领取时间</th>
                                        <th>销售员</th>
                                    </thead>
                                    <tbody>
                                    @foreach($scan_logs as $log)
                                        <tr role="row">
                                            <td>{{ $log->id }}</td>
                                            <td>{{ $log->code->code or '未知' }}</td>
                                            <td>
                                               {{ $log->typeDisplay }}
                                            </td>
                                            <td>
                                               @if (in_array($log->typeDetail, ['waiter_user_scan_over', 'waiter_scan_user_no']))
                                                   {{ $log->user->name or '' }}
                                               @elseif ($log->typeDetail == 'user_scan_waiter_no')
                                                    [暂无服务员扫描]
                                                @elseif ($log->typeDetail == 'waiter_owner_scan_over')
                                                    {{ $log->waiter->name or '' }}
                                                @elseif ($log->typeDetail == 'unKnown')
                                                    [已删除服务员]
                                               @endif
                                            </td>
                                            <td>
                                                @if (in_array($log->typeDetail, ['waiter_user_scan_over', 'user_scan_waiter_no']))
                                                    {{ $log->net_user_id or '' }}
                                                @elseif ($log->typeDetail == 'waiter_scan_user_no')
                                                    [暂无用户扫描]
                                                @elseif ($log->typeDetail == 'waiter_owner_scan_over')
                                                    <span class="text-red">[无]</span>
                                                @elseif ($log->typeDetail == 'unKnown')
                                                    [已删除用户]
                                                @endif
                                            </td>
                                            <td>
                                                @if (in_array($log->typeDetail, ['waiter_user_scan_over', 'user_scan_waiter_no']))
                                                    {{ $log->net_user_name or '' }}
                                                @elseif ($log->typeDetail == 'waiter_scan_user_no')
                                                    [暂无用户扫描]
                                                @elseif ($log->typeDetail == 'waiter_owner_scan_over')
                                                    <span class="text-red">[无]</span>
                                                @elseif ($log->typeDetail == 'unKnown')
                                                    [已删除用户]
                                                @endif
                                            </td>
                                            <td>
                                                @if (in_array($log->typeDetail, ['waiter_user_scan_over', 'waiter_scan_user_no', 'user_scan_waiter_no']))
                                                    <span class="text-red">[无]</span>
                                                @elseif ($log->typeDetail == 'waiter_owner_scan_over')
                                                    {{ $log->user->name or '' }}
                                                @elseif ($log->typeDetail == 'unKnown')
                                                    [已删除店长]
                                                @endif
                                            </td>
                                            <td>{{ $log->shop->name or '' }}</td>
                                            <td>￥{{ $log->money / 100 }}</td>
                                            <td>{{ $log->point }}积分</td>
                                            <td>{{ $log->created_at }}</td>
                                            <td>
                                                @if ($log->shop != null && $log->shop->salesman != null)
                                                    {{ $log->shop->salesman->name }} ({{ $log->shop->salesman->phone }})
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-3">
                                <div class="dataTables_info" role="status" aria-live="polite">{{$scan_logs->firstItem()}} - {{$scan_logs->lastItem()}} (共{{$scan_logs->total()}}条记录)</div>
                            </div>
                            <div class="col-sm-9">
                                <div class="dataTables_paginate paging_simple_numbers">
                                    {!! $scan_logs->appends(['daterange' => old('daterange'), 'scan_type' => old('scan_type'), 'type' => old('type'), 'user_name' => old('user_name'), 'shop_name' => old('shop_name'), 'distribution_name' => old('distribution_name'), 'area_id' => old('area_id'), 'code' => old('code'), 'filter_salesman' => old('filter_salesman')])->links() !!}
                                </div>
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
                    删除经销商
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    您是否确认删除该经销商(<span class="distributor-name"></span>), 此操作无法恢复, 请慎用!
                </div>
                <div class="modal-footer">

                    {{ Form::open(array('method' => 'delete', 'id' => 'deleteDistributorForm')) }}
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
    $(this).find('.distributor-name').text($(e.relatedTarget).data('distributor-name'));
    $(this).find('#deleteDistributorForm').attr('action', $(e.relatedTarget).data('href'));
    });
@endsection
