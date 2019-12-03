@extends('admin.layouts.app')

@section('htmlheader_title')
    商城订单管理
@endsection

@section('contentheader_title')
    商城订单管理
@endsection

@section('main-content')
    <div class='row'>
        <div class='col-md-12'>
            {!! Form::open(['route' => ['admin.store.orders.index'], 'method' => 'get']) !!}
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
                                {!! Form::label('daterange', '下单时间段') !!}
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
                                {!! Form::label('filter_item_name', '关键字(商品名称)') !!}
                                {!! Form::text('filter_item_name', old('filter_item_name'), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('filter_user_name', '关键字(用户名)') !!}
                                {!! Form::text('filter_user_name', old('filter_user_name'), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('status', '状态') !!}
                                {!! Form::select('status', ["" => "全部状态", "created" => "未发货", "shipped"=> "已发货"], old('status'), ['class' => 'form-control select2']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button class="btn btn-primary" type="submit"><i class="fa fa-filter"></i> 应用过滤条件</button>
                    <a class="btn btn-warning pull-right" href="{{ route("admin.store.orders.index") }}"><i class="fa fa-trash"></i> 清除过滤条件</a>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

    <div class='row'>
        <div class='col-md-12'>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">订单列表{{ $has_filter ? "(已过滤)" : "" }}</h3>
                    <div class="box-tools pull-right">
                        <a href="{{ route('admin.store.orders.index', array_merge(app('request')->all(), ['export' => 'xls']))}}" class="btn btn-primary" target="_blank"><i class="fa fa-file-excel-o"></i> 导出Excel</a>
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
                                        <th>商品名称</th>
                                        <th>数量</th>
                                        <th>积分</th>
                                        <th>订货人</th>
                                        <th>手机号</th>
                                        <th>终端名称</th>
                                        <th>终端区域</th>
                                        <th>收货人</th>
                                        <th>收货/充值电话</th>
                                        <th>收货地址</th>
                                        <th>发货状态</th>
                                        <th>备注</th>
                                        <th>下单日期</th>
                                        <th>操作</th>
                                    </thead>
                                    <tbody>
                                    @foreach($orders as $order)
                                        <tr role="row">
                                            <td>{{ $order->id }}</td>
                                            <td><a href="{{ route('admin.store.items.edit', $order->item_id)}}">{{ $order->item->name }}</a></td>
                                            <td>{{ $order->amount }}</td>
                                            <td>{{ ($order->UserPointLog && $order->UserPointLog->amount) ? abs($order->UserPointLog->amount) : '' }}</td>
                                            <td>
                                                @if ($order->user != null)
                                                    <a href="{{ route('admin.users.edit', $order->user_id)}}">({{ $order->user_id }}) {{ $order->user->name }}</a>
                                                @else
                                                    [已删除服务员]
                                                @endif
                                            </td>
                                            <td>
                                                @if ($order->user != null)
                                                    {{ $order->user->telephone }}
                                                @endif
                                            </td>
                                            <td>
                                                @if ($order->user != null && $order->user->shop != null)
                                                    {{ $order->user->shop->name }}
                                                @endif
                                            </td>
                                            <td>
                                                @if ($order->user != null && $order->user->shop != null)
                                                    {{ $order->user->shop->area["display"] }}
                                                @endif
                                            </td>
                                            <td>{{ $order->contact_name }}</td>
                                            <td>{{ $order->contact_phone }}</td>
                                            <td>{{ $order->shipping_address }}</td>
                                            <td>{{ $order->contact_name }}</td>
                                            <td>{{ $order->contact_phone }}</td>
                                            <td>{{ $order->statusDisplay }}</td>
                                            <td>{{ $order->remarks }}</td>
                                            <td>{{ $order->created_at }}</td>
                                            <td>
                                                @if ($order->status == 'created')
                                                    <a class="btn btn-xs btn-success" href="#" data-href="{{ route('admin.store.orders.update', $order->id) }}" data-target-status="shipped" data-target-status-display="已发货" data-remarks="{{ $order->remarks }}" data-toggle="modal" data-target="#confirm-update">改为已发货</a>
                                                @elseif ($order->status == 'shipped')
                                                    <a class="btn btn-xs btn-warning" href="#" data-href="{{ route('admin.store.orders.update', $order->id) }}" data-target-status="created" data-target-status-display="未发货" data-toggle="modal" data-target="#confirm-update">改为未发货</a>
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
                                <div class="dataTables_info" role="status" aria-live="polite">{{$orders->firstItem()}} - {{$orders->lastItem()}} (共{{$orders->total()}}条记录)</div>
                            </div>
                            <div class="col-sm-9">
                                <div class="dataTables_paginate paging_simple_numbers">
                                    {!! $orders->appends(['filter_item_name' => old('filter_item_name'), 'filter_user_name' => old('filter_user_name'), 'status' => old('status'), 'daterange' => old('daterange')])->links() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-warning fade" id="confirm-update" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                {!! Form::open(array('method' => 'put', 'id' => 'deleteDistributorForm')) !!}

                <div class="modal-header">
                    修改发货状态
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    您是否确认修改该订单状态为: <span class="order-new-status-display"></span>.
                    <div class="form-group" style="margin-top: 10px;">
                        {!! Form::label('remarks', '备注', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="input-group">
                            {!! Form::textarea('remarks', '', ['class' => 'form-control order-remarks']) !!}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    {!! Form::hidden('status', '', ['class' => 'order-new-status']) !!}
                    <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-outline btn-ok">修改</button>

                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    $('#confirm-update').on('show.bs.modal', function(e) {
        $(this).find('.order-new-status-display').text($(e.relatedTarget).data('target-status-display'));
        $(this).find('.order-new-status').val($(e.relatedTarget).data('target-status'));
        $(this).find('.order-remarks').text($(e.relatedTarget).data('remarks'));
        $(this).find('#deleteDistributorForm').attr('action', $(e.relatedTarget).data('href'));
    });
@endsection
