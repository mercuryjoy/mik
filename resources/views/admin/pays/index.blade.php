@extends('admin.layouts.app')

@section('htmlheader_title')
    支付方式管理
@endsection

@section('contentheader_title')
    支付方式管理 <a href="{{ route('admin.pays.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> 新建支付方式</a>
@endsection

@section('main-content')
    <div class='row'>
        <div class='col-md-12'>
            {!! Form::open(['route' => ['admin.pays.index'], 'method' => 'get']) !!}
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
                                {!! Form::label('status', '状态') !!}
                                {!! Form::select('status', ["" => "全部状态", "0" => "禁用", "1"=> "启用"], old('status'), ['class' => 'form-control select2']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button class="btn btn-primary" type="submit"><i class="fa fa-filter"></i> 应用过滤条件</button>
                    <a class="btn btn-warning pull-right" href="{{ route("admin.pays.index") }}"><i class="fa fa-trash"></i> 清除过滤条件</a>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

    <div class='row'>
        <div class='col-md-12'>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">商品列表</h3>
                </div>
                <div class="box-body table-responsive">
                    <div class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-hover dataTable" role="grid">
                                    <thead>
                                    <tr role="row">
                                        <th>ID</th>
                                        <th>支付方式</th>
                                        <th>描述</th>
                                        <th>是否默认</th>
                                        <th>状态</th>
                                        <th>添加时间</th>
                                        <th>操作</th>
                                    </thead>
                                    <tbody>
                                    @foreach($pays as $pay)
                                        <tr role="row">
                                            <td>{{ $pay->id }}</td>
                                            <td>{{ $pay->payWayDisplay }}</td>
                                            <td>{{ $pay->description }}</td>
                                            <td>{{ $pay->isDefaultDisplay }}</td>
                                            <td>{{ $pay->statusDisplay }}</td>
                                            <td>{{ $pay->created_at }}</td>
                                            <td>
                                                <a class="btn btn-xs btn-primary" href="{{ route('admin.pays.edit', ['id' => $pay['id']]) }}">详情/修改</a>
                                                @if ($pay->status == 1 && $pay->is_default == 0)
                                                    <a class="btn btn-xs btn-warning" href="#" data-href="{{ route('admin.pays.change', $pay->id) }}" data-target-status="0" data-target-type="status" data-target-status-display="禁用" data-target-pay-display="{{ $pay->payWayDisplay }}" data-toggle="modal" data-target="#confirm-change">禁用</a>
                                                @elseif ($pay->status == 0)
                                                    <a class="btn btn-xs btn-success" href="#" data-href="{{ route('admin.pays.change', $pay->id) }}" data-target-status="1" data-target-type="status" data-target-status-display="启用" data-target-pay-display="{{ $pay->payWayDisplay }}" data-toggle="modal" data-target="#confirm-change">启用</a>
                                                @endif
                                                @if ($pay->is_default == 0 && $pay->status == 1)
                                                    <a class="btn btn-xs btn-success" href="#" data-href="{{ route('admin.pays.change', $pay->id) }}" data-target-default="1" data-target-type="default" data-target-default-display="设为默认" data-target-pay-display="{{ $pay->payWayDisplay }}" data-toggle="modal" data-target="#confirm-default">设为默认</a>
                                                @elseif ($pay->is_default == 1 && $pay->status == 1)
                                                    <a class="btn btn-xs btn-warning" href="#" data-href="{{ route('admin.pays.change', $pay->id) }}" data-target-default="0" data-target-type="default" data-target-default-display="取消默认" data-target-pay-display="{{ $pay->payWayDisplay }}" data-toggle="modal" data-target="#confirm-default">取消默认</a>
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
                                <div class="dataTables_info" role="status" aria-live="polite">{{$pays->firstItem()}} - {{$pays->lastItem()}} (共{{$pays->total()}}条记录)</div>
                            </div>
                            <div class="col-sm-9">
                                <div class="dataTables_paginate paging_simple_numbers">
                                    {!! $pays->appends(['status' => old('status')])->links() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-warning fade" id="confirm-change" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                {!! Form::open(array('method' => 'put', 'id' => 'changeStatusForm')) !!}

                <div class="modal-header">
                    修改<span class="order-new-pay-display"></span>状态
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    您是否确认<span class="order-new-status-display"></span>该支付方式？
                </div>
                <div class="modal-footer">
                    {!! Form::hidden('status', '', ['class' => 'order-new-status']) !!}
                    {!! Form::hidden('type', '', ['class' => 'change-type']) !!}
                    <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-outline btn-ok">确定</button>

                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <div class="modal modal-warning fade" id="confirm-default" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                {!! Form::open(array('method' => 'put', 'id' => 'changeDefaultForm')) !!}

                <div class="modal-header">
                    修改<span class="order-new-pay-display"></span>默认状态
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    您是否确认将该支付方式<span class="order-new-default-display"></span>？
                </div>
                <div class="modal-footer">
                    {!! Form::hidden('is_default', '', ['class' => 'order-new-default']) !!}
                    {!! Form::hidden('type', '', ['class' => 'change-type']) !!}
                    <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-outline btn-ok">确定</button>

                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    $('#confirm-change').on('show.bs.modal', function(e) {
        $(this).find('.order-new-status-display').text($(e.relatedTarget).data('target-status-display'));
        $(this).find('.order-new-pay-display').text($(e.relatedTarget).data('target-pay-display'));
        $(this).find('.order-new-status').val($(e.relatedTarget).data('target-status'));
        $(this).find('.change-type').val($(e.relatedTarget).data('target-type'));
        $(this).find('#changeStatusForm').attr('action', $(e.relatedTarget).data('href'));
    });
    $('#confirm-default').on('show.bs.modal', function(e) {
        $(this).find('.order-new-default-display').text($(e.relatedTarget).data('target-default-display'));
        $(this).find('.order-new-pay-display').text($(e.relatedTarget).data('target-pay-display'));
        $(this).find('.order-new-default').val($(e.relatedTarget).data('target-default'));
        $(this).find('.change-type').val($(e.relatedTarget).data('target-type'));
        $(this).find('#changeDefaultForm').attr('action', $(e.relatedTarget).data('href'));
    });
@endsection
