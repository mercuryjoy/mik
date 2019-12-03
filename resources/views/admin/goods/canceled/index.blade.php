@extends('admin.layouts.app')

@section('htmlheader_title')
    取消订单管理
@endsection

@section('contentheader_title')
    取消订单管理
@endsection

@section('main-content')
    <div class='row'>
        <div class='col-md-12'>
            {!! Form::open(['route' => ['admin.goods.canceled.index'], 'method' => 'get']) !!}
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
                                {!! Form::label('filter_shop_name', '关键字(终端名称)') !!}
                                {!! Form::text('filter_shop_name', old('filter_shop_name'), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('filter_salesman_name', '关键字(营销员名称)') !!}
                                {!! Form::text('filter_salesman_name', old('filter_salesman_name'), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('filter_status', '订单状态') !!}
                                {!! Form::select('filter_status', ["" => "全部状态", "drawback" => "取消订单待审核 	", "canceled"=> "已取消"], old('filter_status'), ['class' => 'form-control select2']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('filter_is_checked', '财务审核状态') !!}
                                {!! Form::select('filter_is_checked', ["" => "全部状态", 'false' => "未审核", "true"=> "已审核"], old('filter_is_checked'), ['class' => 'form-control select2']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button class="btn btn-primary" type="submit"><i class="fa fa-filter"></i> 应用过滤条件</button>
                    <a class="btn btn-warning pull-right" href="{{ route("admin.goods.orders.index") }}"><i class="fa fa-trash"></i> 清除过滤条件</a>
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
                                        <th>订货人</th>
                                        <th>收货人</th>
                                        <th>手机号</th>
                                        <th>收货地址</th>
                                        <th>终端名称</th>
                                        <th>营销员</th>
                                        <th>是否付款</th>
                                        <th>支付方式</th>
                                        <th>财务审核状态</th>
                                        <th>订单状态</th>
                                        <th>备注</th>
                                        <th>下单日期</th>
                                        <th>操作</th>
                                    </thead>
                                    <tbody>
                                    @foreach($orders as $order)
                                        <tr role="row">
                                            <td>{{ $order->id }}</td>
                                            <td><a href="{{ route('admin.goods.items.edit', $order->item_id)}}">{{ $order->item->name }}</a></td>
                                            <td>{{ $order->amount }}</td>
                                            <td>
                                                @if ($order->user != null)
                                                    <a href="{{ route('admin.users.edit', $order->user_id)}}">({{ $order->user_id }}) {{ $order->user->name }}</a>
                                                @else
                                                    [已删除服务员]
                                                @endif
                                            </td>
                                            <td>
                                                {{ $order->contact_name }}
                                            </td>
                                            <td>
                                                {{ $order->contact_phone }}
                                            </td>
                                            <td>{{ $order->shipping_address }}</td>
                                            <td>
                                                @if ($order->user != null && $order->user->shop != null)
                                                    {{ $order->user->shop->name }}
                                                @endif
                                            </td>
                                            <td>{{ $order->salesman->name or ''}}</td>
                                            <td>{{ $order->isPayDisplay }}</td>
                                            <td>{{ $order->payWayDisplay }}</td>
                                            <td>{{ $order->isCheckedDisplay }}</td>
                                            <td>{{ $order->statusDisplay }}</td>
                                            <td>{{ $order->remarks }}</td>
                                            <td>{{ $order->created_at }}</td>
                                            <td>
                                                @if ($order->status == 'drawback')
                                                    <a class="btn btn-xs btn-warning" href="#" data-href="{{ route('admin.goods.canceled.update', $order->id) }}" data-target-status="canceled" data-target-status-display="{{ $order->id }}" data-toggle="modal" data-target="#confirm-update">审核</a>
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
                                    {!! $orders->appends(['daterange' => old('daterange'),'filter_shop_name' => old('filter_shop_name'), 'filter_salesman_name' => old('filter_salesman_name'), 'filter_item_name' => old('filter_item_name'), 'filter_user_name' => old('filter_user_name'), 'status' => old('status')])->links() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-danger fade" id="confirm-update" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                {!! Form::open(array('method' => 'put', 'id' => 'updateStatusForm')) !!}

                <div class="modal-header">
                    确认取消订单 ID: <span class="order-new-status-display"></span>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    此操作不可恢复，您是否确认取消？
                </div>
                <div class="modal-footer">
                    {!! Form::hidden('status', '', ['class' => 'order-new-status']) !!}
                    <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-outline btn-ok">确定</button>

                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    $('#confirm-update').on('show.bs.modal', function(e) {
        $(this).find('.order-new-update').val($(e.relatedTarget).data('target-checked'));
        $(this).find('.order-new-status').val($(e.relatedTarget).data('target-status'));
        $(this).find('#updateStatusForm').attr('action', $(e.relatedTarget).data('href'));
    });
@endsection
