@extends('admin.layouts.app')

@section('htmlheader_title')
    餐饮类型管理
@endsection

@section('contentheader_title')
    餐饮类型管理 <a href="{{ route('admin.categories.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> 新建餐饮类型</a>
@endsection

@section('breadcrumb')
@endsection

@section('main-content')
    <div class='row'>
        <div class='col-md-12'>
            {!! Form::open(['route' => ['admin.categories.index'], 'method' => 'get']) !!}
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
                                {!! Form::label('keyword', '关键字(餐饮类型名称)') !!}
                                {!! Form::text('keyword', old('keyword'), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        {{--<div class="col-md-3">--}}
                            {{--<div class="form-group">--}}
                                {{--{!! Form::label('level', '级别') !!}--}}
                                {{--{!! Form::select('level', ["" => "全部餐饮类型", "1" => "一级餐饮类型", "2"=> "二级餐饮类型"], old('level'), ['class' => 'form-control select2']) !!}--}}
                                {{--</select>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    </div>
                </div>
                <div class="box-footer">
                    <button class="btn btn-primary" type="submit"><i class="fa fa-filter"></i> 应用过滤条件</button>
                    <a class="btn btn-warning pull-right" href="{{ route("admin.categories.index") }}"><i class="fa fa-trash"></i> 清除过滤条件</a>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

    <div class='row'>
        <div class='col-md-12'>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">餐饮类型列表{{ $has_filter ? "(已过滤)" : "" }}</h3>
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
                                        {{--<th>级别</th>--}}
                                        {{--<th>上级餐饮类型</th>--}}
                                        <th>创建时间</th>
                                        <th>操作</th>
                                    </thead>
                                    <tbody>
                                    @foreach($categories as $category)
                                        <tr role="row">
                                            <td>{{ $category->id }}</td>
                                            <td>{{ $category->name }}</td>
                                            {{--<td>{{ ($category->level == 1) ? "一级" : "二级" }}</td>--}}
                                            {{--<td>--}}
                                                {{--@if ($category->parentCategory != null)--}}
                                                {{--<a href="{{ route('admin.categories.edit', ['id' => $category->parentCategory->id]) }}">--}}
                                                    {{--{{  $category->parentCategory->name }}--}}
                                                {{--</a>--}}
                                                {{--@endif--}}
                                            {{--</td>--}}
                                            <td>{{ $category->created_at }}</td>
                                            <td>
                                                <a class="btn btn-xs btn-primary" href="{{ route('admin.categories.edit', ['id' => $category['id']]) }}">详情/修改</a>
                                                <a class="btn btn-xs btn-info" href="{{ route('admin.categories.show', ['id' => $category['id']]) }}">报表</a>
                                                <a class="btn btn-xs btn-danger" href="#" data-href="{{ route('admin.categories.destroy', ['id' => $category['id']]) }}" data-distributor-name="{{ $category['name'] }}" data-toggle="modal" data-target="#confirm-delete">删除</a>
                                            </td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-3">
                                <div class="dataTables_info" role="status" aria-live="polite">{{ $categories->firstItem() }} - {{ $categories->lastItem() }} (共{{ $categories->total() }}条记录)</div>
                            </div>
                            <div class="col-sm-9">
                                <div class="dataTables_paginate paging_simple_numbers">
                                    {!! $categories->appends(['keyword' => old('keyword'), 'level' => old('level'), 'daterange' => old('daterange')])->links() !!}
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
                    删除餐饮类型
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    您是否确认删除该餐饮类型(<span class="distributor-name"></span>), 此操作无法恢复, 请慎用!
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
