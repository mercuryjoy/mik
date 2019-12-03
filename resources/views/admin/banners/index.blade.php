@extends('admin.layouts.app')

@section('htmlheader_title')
    轮播图管理
@endsection

@section('contentheader_title')
    轮播图管理 <a href="{{ route('admin.banners.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> 添加轮播图</a>
@endsection

@section('breadcrumb')
@endsection

@section('main-content')
    <div class='row'>
        <div class='col-md-12'>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">轮播图列表</h3>
                </div>
                <div class="box-body table-responsive">
                    <div class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-hover dataTable" role="grid">
                                    <thead>
                                    <tr role="row">
                                        <th>id</th>
                                        <!-- <th>轮播图题图</th> -->
                                        <th>轮播图标题</th>
                                        <th>轮播图原文链接</th>
                                        <th>排序</th>
                                        <th>操作</th>
                                    </thead>
                                    <tbody>
                                    @foreach($banners_list as $banners)
                                        <tr role="row">
                                            <td>{{ $banners['id'] }}</td>
                                            <!-- <td>{{ $banners['title'] }}</td> -->
                                            <td><img height="60" src="{{ $banners['thumbnail_url'] }}" /></td>
                                            <td><a href="{{ $banners['content_url'] }}" target="_blank">打开原文链接</a></td>
                                            <td>{{$banners['order_id']}}</td>
                                            <td>
                                                <a class="btn btn-xs btn-primary" href="{{ route('admin.banners.edit', ['id' => $banners['id']]) }}">详情/修改</a>
                                                <a class="btn btn-xs btn-danger" href="#" data-href="{{ route('admin.banners.destroy', ['id' => $banners['id']]) }}" data-banners-title="{{ $banners['title'] }}" data-toggle="modal" data-target="#confirm-delete">删除</a>
                                            </td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-3">
                                <div class="dataTables_info" role="status" aria-live="polite">{{$banners_list->firstItem()}} - {{$banners_list->lastItem()}} (共{{$banners_list->total()}}条轮播图)</div>
                            </div>
                            <div class="col-sm-9">
                                <div class="dataTables_paginate paging_simple_numbers">
                                    {!! $banners_list->links() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-danger fade" id="confirm-delete" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    删除轮播图
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    您是否确认删除该轮播图"(<span class="banners-title"></span>)", 此操作无法恢复, 请慎用!
                </div>
                <div class="modal-footer">

                    {{ Form::open(array('method' => 'delete', 'id' => 'deletebannersForm')) }}
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
    $(this).find('.banners-title').text($(e.relatedTarget).data('banners-title'));
    $(this).find('#deletebannersForm').attr('action', $(e.relatedTarget).data('href'));
    });
@endsection