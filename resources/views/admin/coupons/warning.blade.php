@extends('admin.layouts.app')

@section('htmlheader_title')
    核销风控管理
@endsection

@section('contentheader_title')
    核销风控管理
@endsection

@section('breadcrumb')
@endsection

@section('main-content')
    <div class='row'>
        <div class='col-md-12'>
            {!! Form::open(['route' => ['admin.scan.warning'], 'method' => 'get']) !!}
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
                                {!! Form::label('daterange', '创建时间') !!}
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
                                {!! Form::label('filter_user_id', '服务员ID') !!}
                                {!! Form::text('filter_user_id', old('filter_user_id'), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('filter_user_name', '服务员名称') !!}
                                {!! Form::text('filter_user_name', old('filter_user_name'), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('filter_shop_id', '终端ID') !!}
                                {!! Form::text('filter_shop_id', old('filter_shop_id'), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('filter_shop_name', '终端名称') !!}
                                {!! Form::text('filter_shop_name', old('filter_shop_name'), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('filter_net_user_id', '核销用户ID') !!}
                                {!! Form::text('filter_net_user_id', old('filter_net_user_id'), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('filter_net_user_name', '核销用户名称') !!}
                                {!! Form::text('filter_net_user_name', old('filter_net_user_name'), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('filter_warning_type', '预警类型') !!}
                                {!! Form::select('filter_warning_type', ["" => "全部", "over_three_one_day" => "每天核销三次及以上", "over_three_one_weekend" => "近7天核销3次及以上", "over_five_one_month" => "近30天核销5次及以上", "keep_two_day" => "连续两天核销", "same_user_scan" => "服务员作弊预警"], old('filter_warning_type'), ['class' => 'form-control select2']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button class="btn btn-primary" type="submit"><i class="fa fa-filter"></i> 应用过滤条件</button>
                    <a class="btn btn-warning pull-right" href="{{ route("admin.scan.warning") }}"><i class="fa fa-trash"></i> 清除过滤条件</a>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

    <div class='row'>
        <div class='col-md-12'>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">优惠券核销记录{{ $has_filter ? "(已过滤)" : "" }}</h3>
                    <div class="box-tools pull-right">

                        {{--<a href="{{ route('admin.scan.warning', array_merge(app('request')->all(), ['export' => 'xls']))}}"--}}
                           {{--class="btn btn-primary" target="_blank"><i class="fa fa-file-excel-o"></i> 导出Excel</a>--}}
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
                                        <th>核销时间</th>
                                        <th>服务员ID</th>
                                        <th>服务名称</th>
                                        <th>终端ID</th>
                                        <th>终端名称</th>
                                        <th>核销用户ID</th>
                                        <th>核销用户名称</th>
                                        <th>核销次数</th>
                                        <th>预警类型</th>
                                    </thead>
                                    <tbody>
                                    @foreach($warnings as $warning)
                                        <tr role="row">
                                            <td>{{ $warning->id }}</td>
                                            <td>{{ $warning->created_at }}</td>
                                            <td>{{ $warning->user_id or '' }}</td>
                                            <td>{{ $warning->user->name or '' }}</td>
                                            <td>{{ $warning->shop_id or '' }}</td>
                                            <td>{{ $warning->shop->name or '' }}</td>
                                            <td>{{ $warning->net_user_id or '' }}</td>
                                            <td>{{ $warning->net_user_name or '' }}</td>
                                            <td>{{ $warning->times or '' }}</td>
                                            <td>{{ $warning->warningTypeDisplay or '' }}</td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-3">
                                <div class="dataTables_info" role="status" aria-live="polite">{{ $warnings->firstItem() }} - {{ $warnings->lastItem() }} (共{{ $warnings->total() }}条记录)</div>
                            </div>
                            <div class="col-sm-9">
                                <div class="dataTables_paginate paging_simple_numbers">
                                    {!! $warnings->appends([
                                        'filter_user_name' => old('filter_user_name'),
                                        'filter_net_user_name' => old('filter_net_user_name'),
                                        'daterange' => old('daterange'),
                                        'filter_shop_name' => old('filter_shop_name'),
                                        'filter_user_id' => old('filter_user_id'),
                                        'filter_shop_id' => old('filter_shop_id'),
                                        'filter_warning_type' => old('filter_warning_type')
                                     ])->links() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
