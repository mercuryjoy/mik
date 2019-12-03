@extends('admin.layouts.app')

@section('htmlheader_title')
    APP版本管理
@endsection

@section('contentheader_title')
    APP版本管理 <a href="{{ route('admin.versions.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> 新建APP版本</a>
@endsection

@section('breadcrumb')
@endsection

@section('main-content')
    <div class='row'>
        <div class='col-md-12'>
            {!! Form::open(['route' => ['admin.versions.index'], 'method' => 'get']) !!}
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
                                {!! Form::label('type', '平台') !!}
                                {!! Form::select('type', ["ios" => "Ios", "android" => "Android", "other"=> "其他"], old('type'), ['class' => 'form-control select2', 'placeholder' => '请选择平台']) !!}
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('is_force_update', '强制升级') !!}
                                {!! Form::select('is_force_update', ["yes" => "是", "no" => "否"], old('is_force_update'), ['class' => 'form-control select2', 'placeholder' => '请选择升级状态']) !!}
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button class="btn btn-primary" type="submit"><i class="fa fa-filter"></i> 应用过滤条件</button>
                    <a class="btn btn-warning pull-right" href="{{ route("admin.versions.index") }}"><i class="fa fa-trash"></i> 清除过滤条件</a>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

    <div class='row'>
        <div class='col-md-12'>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">APP版本列表{{ $has_filter ? "(已过滤)" : "" }}</h3>
                </div>
                <div class="box-body table-responsive">
                    <div class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-hover dataTable" role="grid">
                                    <thead>
                                    <tr role="row">
                                        <th>ID</th>
                                        <th>平台</th>
                                        <th>版本</th>
                                        <th>描述</th>
                                        <th>强制升级</th>
                                        <th>下载二维码</th>
                                        <th>创建时间</th>
                                        <th>操作</th>
                                    </thead>
                                    <tbody>
                                    @foreach($versions as $version)
                                        <tr role="row">
                                            <td>{{ $version->id or '' }}</td>
                                            <td>{{ $version->typeDisplay or '' }}</td>
                                            <td>{{ $version->version or '' }}</td>
                                            <td>{{ $version->description or '' }}</td>
                                            <td>{{ $version->isForceUpdateDisplay or '' }}</td>
                                            <td>
                                                <p>
                                                    @if ($version->version_code) <a href="{{ $version->version_code or '' }}" target="_blank"><img width="50" src="{{ $version->version_code or '' }}" /></a> @else 暂无 @endif
                                                </p>
                                            </td>
                                            <td>{{ $version->created_at or '' }}</td>
                                            <td>
                                                <a class="btn btn-xs btn-primary" href="{{ route('admin.versions.edit', ['id' => $version->id]) }}">详情/修改</a>
                                                <a class="btn btn-xs btn-danger" href="#" data-href="{{ route('admin.versions.destroy', ['id' => $version->id]) }}" data-distributor-name="{{ $version->name }}" data-toggle="modal" data-target="#confirm-delete">删除</a>
                                            </td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-3">
                                <div class="dataTables_info" role="status" aria-live="polite">{{ $versions->firstItem() }} - {{ $versions->lastItem() }} (共{{ $versions->total() }}条记录)</div>
                            </div>
                            <div class="col-sm-9">
                                <div class="dataTables_paginate paging_simple_numbers">
                                    {!! $versions->appends(['filter_type' => old('filter_type'), 'is_force_update' => old('is_force_update'), 'daterange' => old('daterange')])->links() !!}
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
                    删除APP版本
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    您是否确认删除该APP版本(<span class="distributor-name"></span>), 此操作无法恢复, 请慎用!
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
