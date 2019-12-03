@extends('admin.layouts.app')

@section('htmlheader_title')
    用户扫码记录
@endsection

@section('contentheader_title')
    用户扫码记录
@endsection

@section('main-content')
    <div class='row'>
        <div class='col-md-12'>
            {!! Form::open(['route' => ['admin.net.index'], 'method' => 'get']) !!}
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
                                {!! Form::label('filter_code', '二维码') !!}
                                {!! Form::text('filter_code', old('filter_code'), ['class' => 'form-control', 'placeholder' => '二维码']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('filter_user_id', '服务员ID') !!}
                                {!! Form::text('filter_user_id', old('filter_user_id'), ['class' => 'form-control', 'placeholder' => '服务员ID']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('filter_user_name', '关键字(服务员名)') !!}
                                {!! Form::text('filter_user_name', old('filter_user_name'), ['class' => 'form-control', 'placeholder' => '服务员名']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('filter_salesman', '关键字(销售员姓名)') !!}
                                {!! Form::text('filter_salesman', old('filter_salesman'), ['class' => 'form-control', 'placeholder' => '销售员姓名']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('filter_shop_name', '关键字(终端名称)') !!}
                                {!! Form::text('filter_shop_name', old('filter_shop_name'), ['class' => 'form-control', 'placeholder' => '终端名称']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('filter_distribution_name', '关键字(经销商名称)') !!}
                                {!! Form::text('filter_distribution_name', old('filter_distribution_name'), ['class' => 'form-control', 'placeholder' => '经销商名称']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('filter_scan_type', '扫码类型') !!}
                                {!! Form::select('filter_scan_type', ["" => "全部扫码状态", "new_net_user"=> "新用户扫码", "old_net_user" => "老用户扫码", "net_no_scan" => "用户未扫码", "user_no_scan" => "服务员未扫码"], old('filter_scan_type'), ['class' => 'form-control select2']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button class="btn btn-primary" type="submit"><i class="fa fa-filter"></i> 应用过滤条件</button>
                    <a class="btn btn-warning pull-right" href="{{ route("admin.net.index") }}"><i class="fa fa-trash"></i> 清除过滤条件</a>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

    <div class='row'>
        <div class='col-md-12'>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">用户扫码记录{{ $has_filter ? "(已过滤)" : "" }}</h3>
                    <div class="box-tools pull-right">
                        {{--<a href="{{ route('admin.extra.index', array_merge(app('request')->all(), ['export' => 'xls']))}}" class="btn btn-primary" target="_blank"><i class="fa fa-file-excel-o"></i> 导出Excel</a>--}}
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
                                        <th>服务员ID</th>
                                        <th>服务员</th>
                                        <th>终端</th>
                                        <th>用户ID</th>
                                        <th>用户昵称</th>
                                        <th>收益</th>
                                        <th>领取时间</th>
                                        <th>销售员</th>
                                    </thead>
                                    <tbody>
                                    @foreach($scan_logs as $log)
                                        <tr role="row">
                                            <td>{{ $log->id }}</td>
                                            <td>{{ $log->code->code or '未知' }}</td>
                                            <td>
                                                {{ $log->userTypeDisplay }}
                                            </td>
                                            <td>
                                                @if (in_array($log->typeDetail, ['waiter_user_scan_over', 'waiter_scan_user_no']))
                                                    {{ $log->user_id or '' }}
                                                @elseif ($log->typeDetail == 'user_scan_waiter_no')
                                                    [暂无服务员扫描]
                                                @elseif ($log->typeDetail == 'waiter_owner_scan_over')
                                                    {{ $log->waiter->name or '' }}
                                                @elseif ($log->typeDetail == 'unKnown')
                                                    [已删除服务员]
                                                @endif
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
                                                {{ $log->shop->name or '' }}
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
                                            <td>￥{{ $log->money / 100 }}</td>
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
                                    {!! $scan_logs->appends(['daterange' => old('daterange'), 'filter_code' => old('filter_code'), 'filter_user_id' => old('filter_user_id'), 'filter_user_name' => old('filter_user_name'), 'filter_salesman' => old('filter_salesman'), 'filter_shop_name' => old('filter_shop_name'), 'filter_distribution_name' => old('filter_distribution_name'), 'filter_scan_type' => old('filter_scan_type')])->links() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection