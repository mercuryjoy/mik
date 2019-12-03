@extends('admin.layouts.app')

@section('htmlheader_title')
    服务员管理
@endsection

@section('contentheader_title')
    服务员管理 <a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> 新建服务员</a>
@endsection

@section('breadcrumb')
@endsection

@section('main-content')
    <div class='row'>
        {!! Form::open(['route' => ['admin.users.index'], 'method' => 'get']) !!}

        <div class='col-md-3'>
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">统计设定</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('daterange', '时间段') !!}
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    {!! Form::text('daterange', old('daterange'), ['class' => 'daterange form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button class="btn btn-primary" type="submit">应用统计设定</button>
                </div>
            </div>
        </div>

        <div class='col-md-9'>
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
                                {!! Form::label('filter_shop_keyword', '关键字(终端名称)') !!}
                                {!! Form::text('filter_shop_keyword', old('filter_shop'), ['class' => 'form-control']) !!}
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
                                {!! Form::label('status', '审核状态') !!}
                                {!! Form::select('status', ["" => "全部状态", "pending" => "待审核", "normal"=> "正常"], old('status'), ['class' => 'form-control select2']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('filter_salesman_keyword', '关键字(销售员姓名)') !!}
                                {!! Form::text('filter_salesman_keyword', old('filter_salesman_keyword'), ['class' => 'form-control']) !!}
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
                                {!! Form::label('filter_delete_status', '帐号可用状态') !!}
                                {!! Form::select('filter_delete_status', ["" => "全部状态", "1" => "启用", "2"=> "禁用"], old('filter_delete_status'), ['class' => 'form-control select2']) !!}
                            </div>
                        </div>
                        <div class="col-md-9">
                            {!! Form::label('area_id', '地区') !!}
                            {!! Form::text('area_id', old('area_id'), ['class' => 'areapicker form-control hidden']) !!}
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
                    <h3 class="box-title">服务员列表{{ $has_filter ? "(已过滤)" : "" }}</h3>
                    <div class="box-tools pull-right">
                        <a href="{{ route('admin.users.index', array_merge(app('request')->all(), ['export' => 'xls']))}}" class="btn btn-primary" target="_blank"><i class="fa fa-file-excel-o"></i> 导出Excel</a>
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
                                        <th>姓名</th>
                                        <th>性别</th>
                                        <th>终端</th>
                                        <th>地区</th>
                                        <th>总扫码数</th>
                                        <th>扫码获总积分</th>
                                        <th>扫码获总红包额</th>
                                        <th>新用户扫码获总积分</th>
                                        <th>注册日期</th>
                                        <th>销售员</th>
                                        <th>帐号审核状态</th>
                                        <th>帐号可用状态</th>
                                        <th>操作</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($users as $user)
                                        <tr role="row">
                                            <td>{{ $user->id }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->genderDisplay }}</td>
                                            <td><a href="{{ route('admin.shops.edit', ['id' => $user->shop['id']]) }}">{{ $user->shop['name'] }}</a></td>
                                            <td>
                                                @if($user->shop != null && $user->shop->area != null)
                                                    {{ $user->shop->area["display"] }}
                                                @endif
                                            </td>
                                            <td>{{ $user->scanLog->count() }}</td>
                                            <td>{{ $user->scanLog->sum('point') }}</td>
                                            <td>￥{{ $user->scanLog->sum('money') / 100 }}</td>
                                            <td>{{ $user->userScanGetPointLog->sum('point') }}积分</td>
                                            <td>{{ $user->created_at }}</td>
                                            @if ($user->shop != null && $user->shop->salesman != null)
                                                <td>{{ $user->shop->salesman->name }} ({{ $user->shop->salesman->phone }})</td>
                                            @else
                                                <td />
                                            @endif
                                            <td>{{ $user->statusDisplay }}</td>
                                            <td>{{ $user->deletedDisplay }}</td>
                                            <td>
                                                @if ($user->telephone == settings('app.special_account.telephone'))
                                                    <span style="color:red;">此帐号为iOS APP审核专用,切勿操作。</span>
                                                @else

                                                    @if ($user->status == 'pending')
                                                        <a class="btn btn-xs btn-primary" href="#" data-target-status="normal" data-href="{{ route('admin.users.update.status', ['id' => $user['id']]) }}" data-user-name="{{ $user['name'] }}" data-toggle="modal" data-target="#confirm-status-change">通过审核</a>
                                                    @elseif ($user->status == 'normal')
                                                        <a class="btn btn-xs btn-danger" href="#" data-target-status="pending" data-href="{{ route('admin.users.update.status', ['id' => $user['id']]) }}" data-user-name="{{ $user['name'] }}" data-toggle="modal" data-target="#confirm-status-change">取消审核</a>
                                                    @endif
                                                    <a class="btn btn-xs btn-default" href="{{ route('admin.users.edit', ['id' => $user['id']]) }}">详情/修改</a>
                                                    <a class="btn btn-xs btn-default" href="{{ route('admin.users.show', ['id' => $user['id'], 'daterange' => old('daterange')]) }}">报表</a>

                                                    @if ($user->wechat_openid !== null)
                                                        <a class="btn btn-xs btn-danger" href="#"
                                                           data-target-active="0"
                                                           data-href="{{ route('admin.users.untie', ['id' => $user->id]) }}"
                                                           data-user-name="{{ $user->name }}"
                                                           data-toggle="modal"
                                                           data-target="#confirm-untie-change">解绑微信</a>
                                                    @endif

                                                    @if (! $user->deleted_at)
                                                        <a class="btn btn-xs btn-danger" href="#"
                                                           data-target-active="0"
                                                           data-href="{{ route('admin.users.destroy', ['id' => $user->id]) }}"
                                                           data-user-name="{{ $user->name }}"
                                                           data-toggle="modal"
                                                           data-target="#confirm-active-change">禁用</a>
                                                    @else
                                                        <a class="btn btn-xs btn-success" href="#"
                                                           data-target-active="1"
                                                           data-href="{{ route('admin.users.restore', ['id' => $user->id]) }}"
                                                           data-user-name="{{ $user->name }}"
                                                           data-toggle="modal"
                                                           data-target="#confirm-active-change">启用</a>
                                                    @endif
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
                                <div class="dataTables_info" role="status" aria-live="polite">{{$users->firstItem()}} - {{$users->lastItem()}} (共{{$users->total()}}条记录)</div>
                            </div>
                            <div class="col-sm-9">
                                <div class="dataTables_paginate paging_simple_numbers">
                                    {!! $users->appends(['daterange' => old('daterange'), 'filter_daterange' => old('filter_daterange'), 'filter_salesman_keyword' => old('filter_salesman_keyword'), 'filter_shop_keyword' => old('filter_shop_keyword'), 'filter_phone_keyword' => old('filter_phone_keyword'), 'filter_user_keyword' => old('filter_user_keyword'), 'status' => old('status'), 'area_id' => old('area_id'), 'filter_delete_status' => old('filter_delete_status')])->links() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-danger fade" id="confirm-untie-change" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    解绑用户微信账户
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    您是否确认解绑 <span class="untie-change-user"></span> 的微信账户!
                </div>
                <div class="modal-footer">

                    {{ Form::open(array('id' => 'untieChangeForm')) }}
                    {{ Form::hidden('_method', 'delete') }}

                    <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-outline btn-ok">确认</button>
                    {{ Form::close() }}

                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-danger fade" id="confirm-status-change" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    修改服务员审核状态
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    您是否确认<span class="status-change-action"></span>该服务员(<span class="user-name"></span>)!
                </div>
                <div class="modal-footer">

                    {{ Form::open(array('method' => 'put', 'id' => 'statusChangeUserForm')) }}
                    {!! Form::hidden('status')  !!}

                    <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-outline btn-ok">确认</button>
                    {{ Form::close() }}

                </div>
            </div>
        </div>
    </div>

    <!-- 已移动至活动页面
    <div class="modal modal-info fade" id="send-red-envelope-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                {{ Form::open(array('method' => 'post', 'id' => 'sendRedEnvelopeForm', 'class' => 'form-horizontal')) }}
                <div class="modal-header">
                    发送红包
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>×</span></button>
                </div>
                <div class="modal-body">
                    给该服务员(<span class="user-name"></span>)发红包:
    
                    <div class="form-group" style="margin-top: 10px;">
                        {!! Form::label('money_amount', '金额', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="input-group">
                            {!! Form::number('money_amount', '', ['class' => 'form-control', 'min' => '0.01', 'step' => '0.01', 'max' => '200']) !!}
                            <span>(红包金额需在0.01~200之间)</span>
                        </div>
                    </div>
                    <div class="form-group" style="margin-top: 10px;">
                        {!! Form::label('point_amount', '积分', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="input-group">
                            {!! Form::number('point_amount', '', ['class' => 'form-control', 'min' => '1', 'step' => '0.01', 'max' => '1000']) !!}
                            <span>(积分数量需在1~1000之间)</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-outline btn-ok">确认</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div> -->

    <div class="modal modal-danger fade" id="confirm-active-change" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    修改服务员帐号可用状态
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    您是否确认<span class="active-change-action"></span>该服务员(<span class="user-name"></span>)!
                </div>
                <div class="modal-footer">

                    {{ Form::open(array('id' => 'activeChangeForm')) }}
                    {{ Form::hidden('_method', 'put', ['id' => 'activeChange']) }}

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
        $(this).find('.user-name').text($(e.relatedTarget).data('user-name'));
        $(this).find('#statusChangeUserForm').attr('action', $(e.relatedTarget).data('href'));

        var target_status = $(e.relatedTarget).data('target-status');
        $(this).find('.status-change-action').text((target_status == 'normal') ? '通过审核' : '取消审核');
        $(this).find('input[name="status"]').val(target_status);

        if (target_status == 'normal') {
            $(this).removeClass('modal-danger');
            $(this).addClass('modal-primary');
        } else {
            $(this).removeClass('modal-primary');
            $(this).addClass('modal-danger');
        }
    });

    $('#send-red-envelope-modal').on('show.bs.modal', function(e) {
        $(this).find('.user-name').text($(e.relatedTarget).data('user-name'));
        $(this).find('#sendRedEnvelopeForm').attr('action', $(e.relatedTarget).data('href'));
    });


    $('#confirm-active-change').on('show.bs.modal', function(e) {
        $(this).find('.user-name').text($(e.relatedTarget).data('user-name'));
        $(this).find('#activeChangeForm').attr('action', $(e.relatedTarget).data('href'));
        var target_active = $(e.relatedTarget).data('target-active');
        $(this).find('.active-change-action').text((target_active == 1) ? '启用' : '禁用');
        if (target_active == 0) {
            $('#activeChange').val('delete')
        }
    });


    $('#confirm-untie-change').on('show.bs.modal', function(e) {
        $(this).find('.untie-change-user').text($(e.relatedTarget).data('user-name'));
        $(this).find('#untieChangeForm').attr('action', $(e.relatedTarget).data('href'));
    });

@endsection
