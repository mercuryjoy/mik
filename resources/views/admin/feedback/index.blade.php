@extends('admin.layouts.app')

@section('htmlheader_title')
    用户反馈
@endsection

@section('contentheader_title')
    用户反馈
@endsection

@section('main-content')
    <div class='row'>
        {!! Form::open(['route' => ['admin.feedbacks.index'], 'method' => 'get']) !!}

        <div class='col-md-12'>
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
                                {!! Form::label('filter_daterange', '创建时间') !!}
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    {!! Form::text('filter_daterange', old('filter_daterange'), ['class' => 'daterange form-control']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('filter_user_keyword', '关键字(服务员姓名)') !!}
                                {!! Form::text('filter_user_keyword', old('filter_user_keyword'), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('filter_phone_keyword', '关键字(手机号码)') !!}
                                {!! Form::text('filter_phone_keyword', old('filter_phone_keyword'), ['class' => 'form-control']) !!}
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('status', '回复状态') !!}
                                {!! Form::select('status', ["" => "全部状态", "no" => "待回复", "reply"=> "已回复"], old('status'), ['class' => 'form-control select2']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button class="btn btn-primary" type="submit"><i class="fa fa-filter"></i> 应用过滤条件</button>
                    <a class="btn btn-warning pull-right" href="{{ route("admin.users.index", ['daterange' => old('daterange')]) }}"><i class="fa fa-trash"></i> 清除过滤条件</a>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
    <div class='row'>
        <div class='col-md-12'>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">反馈意见列表</h3>
                    <div class="box-tools pull-right">
                        <a href="{{ route('admin.feedbacks.index', [ 'export' => 'allxls' ]) }}" class="btn btn-primary" target="_blank"><i class="fa fa-file-excel-o"></i> 导出Excel</a>
                    </div>
                </div>
                <div class="box-body table-responsive">
                    <div class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-hover dataTable" role="grid" style="table-layout: fixed;">
                                    <thead>
                                    <tr role="row">
                                        <th>服务员ID</th>
                                        <th style="width: 5em;">服务员</th>
                                        <th>服务员手机号</th>
                                        <th>内容</th>
                                        <th style="width: 120px;">时间</th>
                                        <th>回复状态</th>
                                        <th  style="width: 13rem">操作</th>
                                    </thead>
                                    <tbody>
                                    @foreach($feedbacks as $feedback)
                                        <tr role="row">
                                            <td>
                                                @if ($feedback->user != null)
                                                    {{ $feedback->user->id }}
                                                @else
                                                    无
                                                @endif
                                            </td>
                                            <td>
                                                @if ($feedback->user != null)
                                                    <a href="{{ route('admin.users.edit', $feedback->user_id) }}">{{ $feedback->user->name }}</a>
                                                @else
                                                    匿名
                                                @endif
                                            </td>
                                            <td>
                                                @if ($feedback->user != null)
                                                    {{ $feedback->user->telephone }}
                                                @else
                                                    无
                                                @endif
                                            </td>
                                            <td style="word-break: break-all;">{{ $feedback->content }}</td>
                                            <td>{{ $feedback->created_at }}</td>
                                            <td>
                                            {{ $feedback->statusDisplay }}
                                            </td>
                                            <td>   
                                                <a class="btn btn-xs btn-primary" href="{{ route('admin.feedbacks.show', ['id' => $feedback['id']]) }}">回复</a>

                                                
                                                <a class="btn btn-xs btn-primary" href="{{ route('admin.feedbacks.edit', ['id' => $feedback['id']]) }}">查看详情</a>

                                                <a class="btn btn-xs btn-danger" href="#" data-href="{{ route('admin.feedbacks.destroy', ['id' => $feedback['id']]) }}"  data-toggle="modal" data-target="#confirm-delete">删除</a>
                                            </td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-3">
                                <div class="dataTables_info" role="status" aria-live="polite">{{$feedbacks->firstItem()}} - {{$feedbacks->lastItem()}} (共{{$feedbacks->total()}}条记录)</div>
                            </div>
                            <div class="col-sm-9">
                                <div class="dataTables_paginate paging_simple_numbers">
                                    {!! $feedbacks->links() !!}
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