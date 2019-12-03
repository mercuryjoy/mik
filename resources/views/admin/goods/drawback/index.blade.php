@extends('admin.layouts.app')

@section('htmlheader_title')
    退款单审核
@endsection

@section('contentheader_title')
    退款单审核
@endsection

@section('main-content')
    <div class='row'>
        <div class='col-md-12'>
            {!! Form::open(['route' => ['admin.goods.drawback.index'], 'method' => 'get']) !!}
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
                                {!! Form::label('filter_order_id', '订单ID') !!}
                                {!! Form::number('filter_order_id', old('filter_order_id'), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button class="btn btn-primary" type="submit"><i class="fa fa-filter"></i> 应用过滤条件</button>
                    <a class="btn btn-warning pull-right" href="{{ route("admin.goods.drawback.index") }}"><i class="fa fa-trash"></i> 清除过滤条件</a>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

    <div class='row'>
        <div class='col-md-12'>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">退款单列表{{ $has_filter ? "(已过滤)" : "" }}</h3>
                </div>
                <div class="box-body table-responsive">
                    <div class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-hover dataTable" role="grid">
                                    <thead>
                                    <tr role="row">
                                        <th>ID</th>
                                        <th>订单ID</th>
                                        <th>支付方式</th>
                                        <th>原始金额(元)</th>
                                        <th>退款金额(元)</th>
                                        <th>退款来源</th>
                                        <th>退款状态</th>
                                        <th>创建时间</th>
                                        <th>操作</th>
                                    </thead>
                                    <tbody>
                                    @foreach($orderDrawbacks as $orderDrawback)
                                        <tr role="row">
                                            <td>{{ $orderDrawback->id }}</td>
                                            <td>{{ $orderDrawback->store_order_id }}</td>
                                            <td>{{ $orderDrawback->payWayDisplay }}</td>
                                            <td>{{ $orderDrawback->pay_money / 100 }}</td>
                                            <td>{{ $orderDrawback->drawback_money / 100 }}</td>
                                            <td>{{ $orderDrawback->sourceDisplay }}</td>
                                            <td>{{ $orderDrawback->statusDisplay }}</td>
                                            <td>{{ $orderDrawback->created_at }}</td>
                                            <td>
                                                @if ($orderDrawback->status == 'check')
                                                    <a class="btn btn-xs btn-success" href="#" data-href="{{ route('admin.goods.drawback.update', $orderDrawback->id) }}" data-target-status="finished" data-target-status-display="退款审核" data-toggle="modal" data-target="#confirm-update">退款审核</a>
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
                                <div class="dataTables_info" role="status" aria-live="polite">{{$orderDrawbacks->firstItem()}} - {{$orderDrawbacks->lastItem()}} (共{{$orderDrawbacks->total()}}条记录)</div>
                            </div>
                            <div class="col-sm-9">
                                <div class="dataTables_paginate paging_simple_numbers">
                                    {!! $orderDrawbacks->appends(['daterange' => old('daterange')])->links() !!}
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
                    审核订单
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    请确认对方收到款项后再审核订单，您是否确认审核？
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
        $(this).find('.order-new-status-display').text($(e.relatedTarget).data('target-status-display'));
        $(this).find('.order-new-status').val($(e.relatedTarget).data('target-status'));
        $(this).find('.order-remarks').text($(e.relatedTarget).data('remarks'));
        $(this).find('#updateStatusForm').attr('action', $(e.relatedTarget).data('href'));
    });
@endsection
