@extends('admin.layouts.app')

@section('htmlheader_title')
    管理员管理
@endsection

@section('contentheader_title')
    管理员管理 <a href="{{ route('admin.admins.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> 新建管理员</a>
@endsection

@section('main-content')
    <div class='row'>
        <div class='col-md-12'>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">管理员列表</h3>
                </div>
                <div class="box-body table-responsive">
                    <div class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-hover dataTable" role="grid">
                                    <thead>
                                    <tr role="row">
                                        <th>姓名</th>
                                        <th>电子邮件</th>
                                        <th>级别</th>
                                        <th>创建时间</th>
                                        <th>操作</th>
                                    </thead>
                                    <tbody>
                                    @foreach($admins as $admin)
                                        <tr role="row" class="odd">
                                            <td>{{ $admin['name'] }}</td>
                                            <td>{{ $admin['email'] }}</td>
                                            <td>{{ $admin->levelDisplay }}</td>
                                            <td>{{ $admin['created_at'] }}</td>
                                            <td>
                                                <a class="btn btn-xs btn-primary" href="{{ route('admin.admins.edit', ['id' => $admin->id]) }}">详情/修改</a>
                                                @if (Auth::user()->isSeniorAdmin() && Auth::user()->id != $admin->id)
                                                    <a class="btn btn-xs btn-danger" href="#" data-href="{{ route('admin.admins.destroy', ['id' => $admin['id']]) }}" data-admin-name="{{ $admin['name'] }}" data-toggle="modal" data-target="#confirm-delete">删除</a>
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
                                <div class="dataTables_info" role="status" aria-live="polite">共{{$admins->count()}}名</div>
                            </div>
                            <div class="col-sm-9">
                                <div class="dataTables_paginate paging_simple_numbers">{!! $admins->links() !!}</div>
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
                    删除管理员
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    您是否确认删除该管理员(<span class="admin-name"></span>), 此操作无法恢复, 请慎用!
                </div>
                <div class="modal-footer">

                    {{ Form::open(array('method' => 'delete', 'id' => 'deleteAdminForm')) }}
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
    $(this).find('.admin-name').text($(e.relatedTarget).data('admin-name'));
    $(this).find('#deleteAdminForm').attr('action', $(e.relatedTarget).data('href'));
    });
@endsection
