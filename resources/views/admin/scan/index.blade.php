@extends('admin.layouts.app')

@section('htmlheader_title')
    扫码记录
@endsection

@section('contentheader_title')
    扫码记录
@endsection

@section('main-content')
    <div class='row'>
        <div class='col-md-12'>
            {!! Form::open(['route' => ['admin.scans.index'], 'method' => 'get']) !!}
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
                                {!! Form::label('code', '二维码') !!}
                                {!! Form::text('code', old('code'), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('user_name', '关键字(服务员名)') !!}
                                {!! Form::text('user_name', old('user_name'), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('filter_salesman', '关键字(销售员姓名)') !!}
                                {!! Form::text('filter_salesman', old('filter_salesman'), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('shop_name', '关键字(终端名称)') !!}
                                {!! Form::text('shop_name', old('shop_name'), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('distribution_name', '关键字(经销商名称)') !!}
                                {!! Form::text('distribution_name', old('distribution_name'), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('filter_user_id', '关键字(服务员ID)') !!}
                                {!! Form::text('filter_user_id', old('filter_user_id'), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            {!! Form::label('area_id', '地区') !!}
                            {!! Form::text('area_id', old('area_id'), ['class' => 'areapicker form-control hidden']) !!}
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button class="btn btn-primary" type="submit"><i class="fa fa-filter"></i> 应用过滤条件</button>
                    <a class="btn btn-warning pull-right" href="{{ route("admin.scans.index") }}"><i class="fa fa-trash"></i> 清除过滤条件</a>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

    <div class='row'>
        <div class='col-md-12'>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">扫码记录{{ $has_filter ? "(已过滤)" : "" }}</h3>
                    <div class="box-tools pull-right">
                        <a href="{{ route('admin.scans.index', array_merge(app('request')->all(), ['export' => 'xls']))}}" class="btn btn-primary" target="_blank"><i class="fa fa-file-excel-o"></i> 导出Excel</a>
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
                                        <th>服务员ID</th>
                                        <th>服务员</th>
                                        <th>终端</th>
                                        <th>LuckId</th>
                                        <th>红包金额</th>
                                        <th>领取时间</th>
                                        <th>销售员</th>
                                        <th>经销商</th>
                                        <th>餐饮类型</th>
                                    </thead>
                                    <tbody>
                                    @foreach($scan_logs as $log)
                                        <tr role="row">
                                            <td>{{ $log->id }}</td>
                                            <td>{{ $log->code->code or '[已删除二维码]' }}</td>
                                            <td>{{ $log->user_id }}</td>
                                            <td>
                                                @if($log->user != null)
                                                    <a href="{{ route('admin.users.edit', ['id' => $log->user_id]) }}">
                                                        {{ $log->user->name }}
                                                    </a>
                                                @else
                                                    [已删除用户]
                                                @endif
                                            </td>
                                            <td>
                                                @if($log->shop != null)
                                                    <a href="{{ route('admin.shops.edit', ['id' => $log->shop_id]) }}">
                                                        {{  $log->shop->name }}
                                                    </a>
                                                @else
                                                    [已删除终端]
                                                @endif
                                            </td>
                                            <td>{{ $log->luck_id }}</td>
                                            <td>￥{{ $log->money / 100 }}</td>
                                            <td>{{ $log->created_at }}</td>
                                            <td>
                                            @if ($log->salesman != null && $log->salesman->name != null)
                                                {{ $log->salesman->name }} ({{ $log->salesman->phone }})
                                            @elseif ($log->shop != null && $log->shop->salesman != null)
                                                {{ $log->shop->salesman->name }} ({{ $log->shop->salesman->phone }})
                                            @endif
                                            </td>
                                            <td>
                                                @if ($log->distributor != null && $log->distributor->name != null)
                                                    {{ $log->distributor->name }}
                                                @endif
                                            </td>
                                            <td>
                                                @if ($log->shop != null && $log->shop->category != null)
                                                    {{ $log->shop->category->name }}
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
                                    {!! $scan_logs->appends(['daterange' => old('daterange'), 'code' => old('code'), 'user_name' => old('user_name'), 'shop_name' => old('shop_name'), 'distribution_name' => old('distribution_name'), 'area_id' => old('area_id'), 'filter_user_id' => old('filter_user_id'), 'filter_salesman' => old('filter_salesman')])->links() !!}
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
