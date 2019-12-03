@extends('admin.layouts.app')

@section('htmlheader_title')
    通知管理
@endsection

@section('contentheader_title')
    通知管理 <a href="{{ route('admin.replies.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> 添加回复</a>
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
                                        <th>用户名</th>
                                        <th>手机号</th>
                                        <th>通知标题</th>
                                        <th>通知内容</th>
                                        <th>操作</th>
                                    </thead>
                                    <tbody>
                                    @foreach($replist as $replies)
                                        <tr role="row">
                                            <td>{{ $replies['id'] }}</td>
                                            <td>{{ $replies->user->name }}</td>
                                            <td>{{ $replies->user->telephone }}</td>
                                            <td>{{ $replies['content'] }}</td>
                                            
                                            
                                            <td>{{ $replies->created_at }}
                                            </td>
                                            <td>
                                                
                                                <a class="btn btn-xs btn-danger" href="#" data-href="{{ route('admin.replies.destroy', ['id' => $replies['id']]) }}" data-toggle="modal" data-target="#confirm-delete">删除</a>
                                            </td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-3">
                                <div class="dataTables_info" role="status" aria-live="polite">{{$replist->firstItem()}} - {{$replist->lastItem()}} (共{{$replist->total()}}条通知)</div>
                            </div>
                            <div class="col-sm-9">
                                <div class="dataTables_paginate paging_simple_numbers">
                                    {!! $replist->links() !!}
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
                    您是否确认删除该通知"(<span class="replies-title"></span>)", 此操作无法恢复, 请慎用!
                </div>
                <div class="modal-footer">

                    {{ Form::open(array('method' => 'delete', 'id' => 'deleterepliesForm')) }}
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
    $(this).find('.replies-title').text($(e.relatedTarget).data('replies-title'));
    $(this).find('#deleterepliesForm').attr('action', $(e.relatedTarget).data('href'));
    });
@endsection