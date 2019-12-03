@extends('admin.layouts.app')

@inject('salesman', 'App\Salesman')

@section('htmlheader_title')
    营销员管理
@endsection

@section('contentheader_title')
    营销员管理{{-- <a href="{{ route('admin.salesmen.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> 新建营销员</a>--}}
@endsection

@section('breadcrumb')
@endsection

@section('main-content')
    <div class='row'>
        <div class='col-md-12'>
            {!! Form::open(['route' => ['admin.salesmen.index'], 'method' => 'get']) !!}
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
                                {!! Form::label('filter_id', '关键字(id)') !!}
                                {!! Form::text('filter_id', old('filter_id'), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('filter_name', '关键字(营销员)') !!}
                                {!! Form::text('filter_name', old('filter_name'), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('filter_phone', '关键字(手机号码)') !!}
                                {!! Form::text('filter_phone', old('filter_phone'), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('filter_status', '状态') !!}
                                {!! Form::select('filter_status', $salesman->status_display, old('filter_status', $filter_status), ['class' => 'form-control', 'placeholder' => '全部状态']) !!}
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
                    <h3 class="box-title">营销员列表</h3>
                </div>
                <div class="box-body table-responsive">
                    <div class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-hover dataTable" role="grid">
                                    <thead>
                                    <tr role="row">
                                        <th>ID</th>
                                        <th>姓名</th>
                                        <th>手机号码</th>
                                        <th>状态</th>
                                    </thead>
                                    <tbody>
                                    @foreach($salesmen as $salesman)
                                        <tr role="row">
                                            <td>{{ $salesman->id }}</td>
                                            <td>{{ $salesman->name }}</td>
                                            <td>{{ $salesman->phone }}</td>
                                            <td>{{ $salesman->statusDisplay }}</td>
                                            <td>
                                                {{--<a class="btn btn-xs btn-primary" href="{{ route('admin.salesmen.edit', ['id' => $salesman->id]) }}">详情/修改</a>--}}
                                                {{--<a class="btn btn-xs btn-danger" href="#" data-href="{{ route('admin.salesmen.destroy', ['id' => $salesman->id]) }}" data-salesman-name="{{ $salesman->name }}" data-toggle="modal" data-target="#confirm-delete">删除</a>--}}
                                            </td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-3">
                                <div class="dataTables_info" role="status" aria-live="polite">{{$salesmen->firstItem()}} - {{$salesmen->lastItem()}} (共{{$salesmen->total()}}条记录)</div>
                            </div>
                            <div class="col-sm-9">
                                <div class="dataTables_paginate paging_simple_numbers">
                                    {!! $salesmen->appends(['filter_status' => old('filter_status'),'filter_id' => old('filter_id'),'filter_name'=> old('filter_phone')])->links() !!}
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
                    删除营销员
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    您是否确认删除该营销员(<span class="salesman-name"></span>), 此操作无法恢复, 请慎用!
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
    $(this).find('.salesman-name').text($(e.relatedTarget).data('salesman-name'));
    $(this).find('#deleteDistributorForm').attr('action', $(e.relatedTarget).data('href'));
    });
@endsection
