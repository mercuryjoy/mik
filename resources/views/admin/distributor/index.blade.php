@extends('admin.layouts.app')

@section('htmlheader_title')
    经销商管理
@endsection

@section('contentheader_title')
    经销商管理 <a href="{{ route('admin.distributors.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> 新建经销商</a>
@endsection

@section('breadcrumb')
@endsection

@section('main-content')
    <div class='row'>
        <div class='col-md-12'>
            {!! Form::open(['route' => ['admin.distributors.index'], 'method' => 'get']) !!}
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
                                {!! Form::label('keyword', '关键字(商户名称)') !!}
                                {!! Form::text('keyword', old('keyword'), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('level', '级别') !!}
                                {!! Form::select('level', ["" => "全部经销商", "1" => "一级经销商", "2"=> "二级经销商"], old('level'), ['class' => 'form-control select2']) !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            {!! Form::label('area_id', '地区') !!}
                            {!! Form::text('area_id', old('area_id'), ['class' => 'areapicker form-control hidden']) !!}
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('filter_status', '状态') !!}
                                {!! Form::select('filter_status', ["" => "全部状态", "1" => "启用", "2"=> "禁用"], old('filter_status'), ['class' => 'form-control select2']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button class="btn btn-primary" type="submit"><i class="fa fa-filter"></i> 应用过滤条件</button>
                    <a class="btn btn-warning pull-right" href="{{ route("admin.distributors.index") }}"><i class="fa fa-trash"></i> 清除过滤条件</a>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

    <div class='row'>
        <div class='col-md-12'>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">经销商列表{{ $has_filter ? "(已过滤)" : "" }}</h3>
                    <div class="box-tools pull-right">
                        <a href="{{ route('admin.distributors.index', [ 'export' => 'allxls' ]) }}" class="btn btn-primary" target="_blank"><i class="fa fa-file-excel-o"></i> 导出Excel</a>
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
                                        <th>名称</th>
                                        <th>级别</th>
                                        <th>上级经销商</th>
                                        <th>地区</th>
                                        <th>创建时间</th>
                                        <th>状态</th>
                                        <th>操作</th>
                                    </thead>
                                    <tbody>
                                    @foreach($distributors as $distributor)
                                        <tr role="row">
                                            <td>{{ $distributor->id }}</td>
                                            <td>{{ $distributor->name }}</td>
                                            <td>{{ ($distributor->level == 1) ? "一级" : "二级" }}</td>
                                            <td>
                                                @if ($distributor->parent_distributor != null)
                                                <a href="{{ route('admin.distributors.edit', ['id' => $distributor->parent_distributor->id]) }}">
                                                    {{  $distributor->parent_distributor->name }}
                                                </a>
                                                @endif
                                            </td>
                                            <td>{{ $distributor->area["display"]}}</td>
                                            <td>{{ $distributor->created_at }}</td>
                                            <td>{{ $distributor->deletedDisplay }}</td>
                                            <td>
                                                <a class="btn btn-xs btn-primary" href="{{ route('admin.distributors.edit', ['id' => $distributor['id']]) }}">详情/修改</a>
                                                <a class="btn btn-xs btn-info" href="{{ route('admin.distributors.show', ['id' => $distributor['id']]) }}">报表</a>
                                                @if (! $distributor->deleted_at)
                                                    <a class="btn btn-xs btn-danger" href="#"
                                                       data-target-status="0"
                                                       data-href="{{ route('admin.distributors.destroy', ['id' => $distributor->id]) }}"
                                                       data-distributor-name="{{ $distributor->name }}"
                                                       data-toggle="modal"
                                                       data-target="#confirm-status-change">禁用</a>
                                                @else
                                                    <a class="btn btn-xs btn-success" href="#"
                                                    data-target-status="1"
                                                    data-href="{{ route('admin.distributors.restore', ['id' => $distributor->id]) }}"
                                                    data-distributor-name="{{ $distributor->name }}"
                                                    data-toggle="modal"
                                                    data-target="#confirm-status-change">启用</a>
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
                                <div class="dataTables_info" role="status" aria-live="polite">{{$distributors->firstItem()}} - {{$distributors->lastItem()}} (共{{$distributors->total()}}条记录)</div>
                            </div>
                            <div class="col-sm-9">
                                <div class="dataTables_paginate paging_simple_numbers">
                                    {!! $distributors->appends(['keyword' => old('keyword'), 'level' => old('level'), 'filter_status' => old('filter_status'), 'area_id' => old('area_id'), 'daterange' => old('daterange')])->links() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-danger fade" id="confirm-status-change" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    修改经销商状态
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    您是否确认<span class="status-change-action"></span>该经销商(<span class="distributor-name"></span>)!
                </div>
                <div class="modal-footer">

                    {{ Form::open(array('id' => 'statusChangeForm')) }}
                    {{ method_field('put') }}
                    <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-outline btn-ok">确认</button>
                    {{ Form::close() }}

                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    $('#confirm-status-change').on('show.bs.modal', function(e) {
        $(this).find('.distributor-name').text($(e.relatedTarget).data('distributor-name'));
        $(this).find('#statusChangeForm').attr('action', $(e.relatedTarget).data('href'));
        var target_status = $(e.relatedTarget).data('target-status');
        $(this).find('.status-change-action').text((target_status == 1) ? '启用' : '禁用');
        if (target_status == 0) {
            $('input[name="_method"]').val('delete')
        }
    });
@endsection
