@extends('admin.layouts.app')

@section('htmlheader_title')
    通知管理
@endsection

@section('contentheader_title')
    通知管理 <a href="{{ route('admin.news.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> 添加通知</a>
@endsection

@section('breadcrumb')
@endsection

@section('main-content')
    <div class='row'>
        <div class='col-md-12'>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">通知列表</h3>
                </div>
                <div class="box-body table-responsive">
                    <div class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-hover dataTable" role="grid">
                                    <thead>
                                    <tr role="row">
                                        <th>ID</th>
                                        <th>通知标题</th>
                                        <th>题图</th>
                                        <th>通知内容图</th>
                                        <th>通知内容</th>
                                        <th>通知原文链接</th>
                                        <th>是否为草稿</th>
                                        <th>操作</th>
                                    </thead>
                                    <tbody>
                                    @foreach($news_list as $news)
                                        <tr role="row">
                                            <td>{{ $news['id'] }}</td>
                                            <td>{{ $news['title'] }}</td>
                                            <td><img height="60" src="{{ $news['picture_url'] }}" /></td>
                                            <td><img height="60" src="{{ $news['thumbnail_url'] }}" /></td>
                                            <td>{{ $news['content'] }}</td>
                                            <td><a href="{{ $news['content_url'] }}" target="_blank">打开原文链接</a></td>
                                            <td>{{ $news->statusDisplay }}
                                            </td>
                                            <td>
                                                <a class="btn btn-xs btn-primary" href="{{ route('admin.news.edit', ['id' => $news['id']]) }}">详情/修改</a>
                                                <a class="btn btn-xs btn-danger" href="#" data-href="{{ route('admin.news.destroy', ['id' => $news['id']]) }}" data-news-title="{{ $news['title'] }}" data-toggle="modal" data-target="#confirm-delete">删除</a>
                                            </td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-3">
                                <div class="dataTables_info" role="status" aria-live="polite">{{$news_list->firstItem()}} - {{$news_list->lastItem()}} (共{{$news_list->total()}}条通知)</div>
                            </div>
                            <div class="col-sm-9">
                                <div class="dataTables_paginate paging_simple_numbers">
                                    {!! $news_list->links() !!}
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
                    删除通知
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    您是否确认删除该通知"(<span class="news-title"></span>)", 此操作无法恢复, 请慎用!
                </div>
                <div class="modal-footer">

                    {{ Form::open(array('method' => 'delete', 'id' => 'deleteNewsForm')) }}
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
    $(this).find('.news-title').text($(e.relatedTarget).data('news-title'));
    $(this).find('#deleteNewsForm').attr('action', $(e.relatedTarget).data('href'));
    });
@endsection